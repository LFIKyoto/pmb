<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb.class.php,v 1.11.6.9 2019-12-03 10:47:42 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/pnb/dilicom.class.php');
require_once($class_path.'/emprunteur.class.php');
require_once($class_path.'/ajax_pret.class.php');
require_once($class_path.'/pret.class.php');
require_once($include_path.'/h2o/pmb_h2o.inc.php');
require_once($include_path."/notice_authors.inc.php");

class pnb {
    
    public function __construct(){}
    
    public function get_devices(){
        global $base_path;
        if(!(file_exists($base_path.'/temp/pnb_devices_list.temp') && ((time()-86400) > filemtime($base_path.'/temp/pnb_devices_list.temp')))){
            $dilicom = new dilicom();
            $data = encoding_normalize::json_decode($dilicom->query('getUserAgent'), true);
            
            $to_sort_devices = array();
            foreach($data['listUserAgent'] as $device) {
                $to_sort_devices[$device['appName']] = $device;
            }
            ksort($to_sort_devices);
            $data['listUserAgent'] = array();
            foreach($to_sort_devices as $device) {
                $data['listUserAgent'][] = $device;
            }
            file_put_contents($base_path.'/temp/pnb_devices_list.temp', encoding_normalize::json_encode($data));
        }
        $devices = encoding_normalize::json_decode(file_get_contents($base_path.'/temp/pnb_devices_list.temp'), true);
        return $devices['listUserAgent'];
    }
    
    public function get_devices_list($empr_id) {
        global $include_path;
        $empr = new emprunteur($empr_id);
        
        $empr_devices = $empr->get_devices();
        $pnb_devices = $this->get_devices();
        foreach($pnb_devices as $key => $device){
            $pnb_devices[$key]['selected'] = false;
            if(in_array($device['userAgentId'], $empr_devices)){
                $pnb_devices[$key]['selected'] = true;
            }
        }
        $h2o = H2o_collection::get_instance($include_path .'/templates/pnb/pnb_devices.tpl.html');
        return $h2o->render(array('devices' => $pnb_devices));
    }
    
    public function save_devices_list($empr_id){
        global $empr_pnb_devices_list;
        $empr = new emprunteur($empr_id);
        $empr->set_devices($empr_pnb_devices_list);
        $empr->save_devices();
    }
    
    public function get_empr_loans($empr_id) {
        global $msg;
        $loans = array();
        $query = 'select pret_idexpl, pret_date, pret_retour, retour_initial, cpt_prolongation
				from pret join pnb_orders_expl on pret_idexpl = pnb_order_expl_num
				where pret_idempr = '.$empr_id;
        
        $sql = "SELECT notices_m.notice_id as num_notice_mono, bulletin_id, IF(pret_retour>sysdate(),0,1) as retard, expl_id," ;
        $sql.= "date_format(pret_retour, '".$msg["format_date_sql"]."') as aff_pret_retour, pret_retour, ";
        $sql.= "date_format(pret_date, '".$msg["format_date_sql"]."') as aff_pret_date, " ;
        $sql.= "trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if(mention_date, concat(' (',mention_date,')') ,if (date_date, concat(' (',date_format(date_date, '".$msg["format_date_sql"]."'),')') ,'')))) as tit, if(notices_m.notice_id, notices_m.notice_id, notices_s.notice_id) as not_id, tdoc_libelle, empr_location, location_libelle ";
        $sql.= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
        $sql.= "        LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
        $sql.= "        LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
        $sql.= "        docs_type, docs_location , pret join pnb_orders_expl on pnb_order_expl_num = pret_idexpl, empr ";
        $sql.= "WHERE expl_typdoc = idtyp_doc and pret_idexpl = expl_id  and empr.id_empr = pret.pret_idempr and expl_location = idlocation ";
        $sql.= " order by location_libelle, pret_retour";
        
        $result = pmb_mysql_query($sql);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $responsab = array("responsabilites" => array(),"auteurs" => array());  // les auteurs
                $responsab = get_notice_authors($row['num_notice_mono']) ;
                
                $as = array_search ("0", $responsab["responsabilites"]) ;
                if ($as!== FALSE && $as!== NULL) {
                    $auteur_0 = $responsab["auteurs"][$as] ;
                    $auteur = new auteur($auteur_0["id"]);
                    $mention_resp = $auteur->get_isbd();
                } else {
                    $as = array_keys ($responsab["responsabilites"], "1" ) ;
                    $aut1_libelle = array();
                    for ($i = 0 ; $i < count($as) ; $i++) {
                        $indice = $as[$i] ;
                        $auteur_1 = $responsab["auteurs"][$indice] ;
                        $auteur = new auteur($auteur_1["id"]);
                        $aut1_libelle[]= $auteur->get_isbd();
                    }
                    $mention_resp = implode (", ",$aut1_libelle) ;
                }
                
                $mention_resp ? $auteur = $mention_resp : $auteur="";
                $loans[] = $row;
                $loans[count($loans)-1]['author'] = $auteur;
            }
        }
        return $loans;
    }
    
    public function get_empr_loans_list($empr_id) {
        global $include_path;
        
        $empr_loans = $this->get_empr_loans($empr_id);
        $h2o = H2o_collection::get_instance($include_path .'/templates/pnb/pnb_empr_loans.tpl.html');
        
        return $h2o->render(array('loans' => $empr_loans));
    }
    
    public function loan_book($empr_id, $record_id, $user_agent) {
        global $pmb_pnb_param_login;
        global $opac_url_base;
        global $pmb_pnb_drm_parameters;
                
        $loaner = new emprunteur($empr_id);
        $order = $this->get_order_from_record_id($record_id);
        if(count($order)){            
            if($this->is_already_borrowed($order['expl_id'], $empr_id)){
                return array("status" => false, "message" => 'pnb_loan_already_borrowed');
            }
            
            $pnb_order_line_id = $this->get_order_line_id_from_order_num($order['order_num']);
            if(!$pnb_order_line_id){
                return array("status" => false, "message" => 'pnb_loan_failed', 'infos' => 'get_order_line_id');
            }  
            
            $pnb_loan = new pnb_loan();
            $pnb_loan->check_pieges(0, $empr_id, 0, $order['expl_id'], false);
            if($pnb_loan->status){                
                return array("status" => false, "message" => 'pnb_loan_failed', 'infos' => 'check_pieges');
            }
                      
            // Durée du prêt issu configuration DRM ACS Adobe uniquement. TO DO pour les autres
            $drm = json_decode($pmb_pnb_drm_parameters, true);                      
            $duree_pret = 0;
            if (isset($drm['ACS']) && $drm['ACS']['loan_duration']) {
                $duree_pret = $drm['ACS']['loan_duration'];
            } else {  
                $duree_pret = 30; // au cas ou drm Adobe pas configuré...
            }
            $query = "SELECT date_add(CURDATE(), INTERVAL $duree_pret DAY) as date_retour";
            file_put_contents('/tmp/debug.txt', print_r($query, true), FILE_APPEND);
            $result=pmb_mysql_query($query);
            $date_retour = pmb_mysql_fetch_row($result)[0];
            file_put_contents('/tmp/debug.txt', print_r($date_retour, true), FILE_APPEND);
            
            // Formatage date_retour pour Dilicom
            $return_date = new DateTime($date_retour);
            $loanEndDate = $return_date->format(DateTime::ISO8601);
            
            //Création de l'URL de retour anticipé
            $returnCallbackContext = [
                'empr_id' => $empr_id,
                'expl_id' => $order['expl_id']
            ];
            $shorturl = new shorturl_type_pnb();
            $returnCallbackHash = $shorturl->generate_hash("returnCallback",$returnCallbackContext);
            
            $param_dilicom = array(
                'glnLoaner' => $pmb_pnb_param_login,
                'glnContractor' => $pmb_pnb_param_login,
                'orderLineId' => $pnb_order_line_id,
                'loanId' => $empr_id . '_' . $order['expl_id'] . '_' . date("Ymd"),
                'ean13' => $this->get_ean_from_record($record_id),
                'accessMedium' => 'DOWNLOAD',
                'localization' => 'EX_SITU',
                'loanEndDate' => $loanEndDate,
                'callBackUrl' => $opac_url_base."s.php?h=".$returnCallbackHash,
                'DRMinfo.'.'userAgent' => NULL,
                'DRMinfo.'.'readerPass' => $loaner->get_pnb_password(),
                'DRMinfo.'.'readerHint' => $loaner->get_pnb_password_hint(),
                'DRMinfo.'.'readerId' => $loaner->cb,
                //'userInfo.'.'year' => intval($loaner->birth),
                'userInfo.'.'year' => rand(1950, 2010),
                'userInfo.'.'gender' => ($loaner->sexe == 1 ? 'H' : 'F'),
            );  
            
            $dilicom = new dilicom();
            $response = $dilicom->query('loanBook', $param_dilicom);
            $response = encoding_normalize::json_decode($response, true);           
            if(!empty($response) && !empty($response['returnStatus'])){
                $response['num_loaner'] = $loaner->id;
                $response['num_expl'] = $order['expl_id'];
                switch($response['returnStatus']) {
                    case 'OK' :
                        $result_pret = $pnb_loan->confirm_pret($empr_id, $order['expl_id'], $date_retour, 'Dilicom');
                        $this->save_pnb_loan_infos($response);
                        $this->increment_loan_counter();
                        return array("status" => true, "message"=> 'pnb_loan_succeed', "infos" => $response);
                        break;
                    case 'RECALL' :
                        $result_pret = $pnb_loan->confirm_pret($empr_id, $order['expl_id'], $date_retour, 'Dilicom');
                        $this->save_pnb_loan_infos($response);
                        return array("status" => true, "message"=> 'pnb_loan_already_borrowed', "infos" => $response);
                        break;
                    default:
                        //Suppression du prêt côté PMB si le prêt n'a pas fonctionné chez Dilicom
                        $query = "delete from pret where pret_idexpl = '".$order['expl_id']."'";
                        pmb_mysql_query($query);
                        return array("status" => false, "message" => 'pnb_loan_failed', 'infos' => $response['returnMessage']);
                        break;
                }
            } else {                
                //Suppression du prêt côté PMB si pas de réponse de Dilicom
                $query = "delete from pret where pret_idexpl = '".$order['expl_id']."'";
                pmb_mysql_query($query);
                return array("status" => false, "message" => 'pnb_loan_failed', 'infos' => "Pas de reponse de Dilicom");               
            }
        }
        return array("status" => false, "message" => 'pnb_loan_failed', 'infos' => "Pas de commande");
    }
    
    public function return_book($empr_id,$expl_id){  
        
        $pret = new expl_to_do('',$expl_id);
        $result = $pret->do_pnb_retour();
        return array("status" => true, "message"=> $result['message'], "infos" => $result['infos']);
    }
    
    /**
     * Retourne un identifiant d'exemplaire et un identifiant de ligne de commande depuis l'identifiant d'une notice
     * @param int $record_id
     *
     * TODO !! Ne pas oublier de tester également la table "pret" afin de
     * trouver un exemplaire qui n'est pas déjà emprunté
     */
    protected function get_order_from_record_id($record_id) {
        $query = 'SELECT expl_id, pnb_order_num as order_num FROM exemplaires
				LEFT JOIN pret ON exemplaires.expl_id = pret.pret_idexpl 
				JOIN pnb_orders_expl ON pnb_orders_expl.pnb_order_expl_num = exemplaires.expl_id
				JOIN pnb_orders ON pnb_orders.id_pnb_order = pnb_orders_expl.pnb_order_num
				WHERE expl_notice = '.$record_id.' AND expl_bulletin = 0 AND pret.pret_idexpl is null
				ORDER BY pnb_orders.pnb_order_offer_date_end ASC';       
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            return pmb_mysql_fetch_assoc($result);
        }
    }
    
    protected function get_order_line_id($order_id){
        $query = "select pnb_order_line_id as order_line_id from pnb_orders where id_pnb_order = ".$order_id;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            return pmb_mysql_fetch_assoc($result);
        }
        return false;
    }
    
    protected function increment_loan_counter(){
        global $pmb_pnb_loan_counter;
        $pmb_pnb_loan_counter++;
        $query = "UPDATE parametres SET valeur_param='".$pmb_pnb_loan_counter."' where type_param= 'pmb' and sstype_param='pnb_loan_counter' ";
        pmb_mysql_query($query);
    }
    
    protected function get_order_line_id_from_order_num($order_num){
        $query = "select pnb_order_line_id as order_line_id from pnb_orders where id_pnb_order= ".$order_num;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            $data = pmb_mysql_fetch_assoc($result);
            return $data['order_line_id'];
        }
        return false;
    }
    
    protected function get_ean_from_record($record_id){
        $query = "select code from notices where notice_id= ".$record_id;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            $data =  pmb_mysql_fetch_assoc($result);
            return str_replace('-', '', $data['code']);
        }
        return false;
    }
    
    protected function save_pnb_loan_infos($infos){
        global $pmb_pnb_loan_counter;
        $query = "INSERT INTO pnb_loans set ";
        $query .= "id_pnb_loan = '".$pmb_pnb_loan_counter."', ";
        $query .= "pnb_loan_order_line_id = '".$infos['orderLineId']."', ";
        $query .= "pnb_loan_link = '".$infos['link']['url']."', ";
        $query .= "pnb_loan_request_id   = '".$infos['requestId']."', ";
        $query .= "pnb_loan_num_expl = '".$infos['num_expl']."', ";
        $query .= "pnb_loan_num_loaner = '".$infos['num_loaner']."', ";
        $query .= "pnb_loan_drm = '".$infos['protection']."' ";
        $result = pmb_mysql_query($query);
    }
    
    public function get_mailto_data($commands_ids){
        global $pmb_pnb_param_login;
        $commands_details = array();
        foreach($commands_ids as $command_id){
            $command_id+=0;
            $command = new pnb_order($command_id);
            $commands_details[] = array(
                'orderId' =>  $command->get_order_id(),
                'orderCreateDate' => $command->get_offer_formated_date(),
                'orderLineId' =>  $command->get_line_id(),
            );
        }
        if(count($commands_details)){
            $commands_details['GLN'] = $pmb_pnb_param_login;
            $commands_details['address'] = 'technique@dilicom.fr';
        }
        return $commands_details;
    }
    
    public function is_already_borrowed($expl_id, $empr_id){
        $query = "select pret.pret_idexpl from pret join pnb_loans on pnb_loans.pnb_loan_num_loaner = pret.pret_idempr and pnb_loans.pnb_loan_num_expl = pret.pret_idexpl where pret.pret_idexpl = ".$expl_id." and pnb_loans.pnb_loan_num_loaner = ".$empr_id;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            return true;
        }
        return false;
    }
    
    public static function delete_pnb_record_links($record_id){
        $record_id+= 0;
        $query = 'select expl_id, expl_cb from exemplaires where expl_notice =' . $record_id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                
                pmb_mysql_query("delete from pret where pret_idexpl ='" . $row['expl_id'] . "' ");
                
                pmb_mysql_query("delete from pnb_loans where pnb_loan_num_expl  ='" . $row['expl_id'] . "' ");
                
                pmb_mysql_query("delete from pnb_orders_expl where pnb_order_expl_num  ='" . $row['expl_id'] . "' ");
                
                exemplaire::del_expl($row['expl_id']);
            }
        }
        pmb_mysql_query("delete from pnb_orders where pnb_order_num_notice  ='" . $record_id . "' ");
    }
}// end class
