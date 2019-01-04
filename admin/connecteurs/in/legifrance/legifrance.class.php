<?php
global $class_path;
require_once($class_path."/curl.class.php");

class legifrance extends connector {
    public function __construct($connector_path="") {
    	parent::__construct($connector_path);
    }
    
    public function get_id() {
    	return "legifrance";
    }
    
    //Est-ce un entrepot ?
    public function is_repository() {
            return 2;
    }
    
    public function enrichment_is_allow(){
        return false;
    }
    
     //Formulaire des propriétés générales
    public function get_property_form() {
        global $charset;
        $this->fetch_global_properties();
        //Affichage du formulaire en fonction de $this->parameters
        $url='';
        if ($this->parameters) {
                $vars = unserialize($this->parameters);
                $url=$vars['url'];
                $limit=$vars['limit'];
        }
        $form="<div class='row'>
                <div class='colonne3'>
                        <label for='url'>".$this->msg["legifrance_url"]."</label>
                </div>
                <div class='colonne_suite'>
                        <input type='text' name='url' id='url' class='saisie-120em' value='".htmlentities($url,ENT_QUOTES,$charset)."'/>
                </div>
        </div>
        <div class='row'>
                <div class='colonne3'>
                        <label for='url'>".$this->msg["legifrance_limit"]."</label>
                </div>
                <div class='colonne_suite'>
                        <input type='text' name='limit' id='url' class='saisie-60em' value='".htmlentities($limit,ENT_QUOTES,$charset)."'/>
                </div>
        </div>";

        $form.="
                <div class='row'></div>
                ";
        return $form;
    }
    
    public function source_get_property_form($source_id) {
    	global $charset;

        $form.="
                <div class='row'></div>
                ";
        return $form;
    }
    
    public function make_serialized_source_properties($source_id) { 	
	$this->sources[$source_id]["PARAMETERS"]=serialize([]);
    }
	
	
    public function make_serialized_properties() {
        global $url,$limit;
        //Mise en forme des paramètres à partir de variables globales (mettre le résultat dans $this->parameters)
        $keys = array();
        $keys['url']= stripslashes($url);
        $keys['limit']= stripslashes($limit)*1;
        $this->parameters = serialize($keys);
    }
    
    public function rec_record($record,$source_id,$search_id,$url) {
        //Initialisation
        $ref="";
        $ufield="";
        $usubfield="";
        $field_order=0;
        $subfield_order=0;
        $value="";
        $date_import=date("Y-m-d H:i:s",time());
        
        $ref = md5($record->uri);
        
        //Si conservation des anciennes notices, on regarde si elle existe
        if (!$this->del_old) {
                $ref_exists = $this->has_ref($source_id, $ref);
        }
        //Si pas de conservation des anciennes notices, on supprime
        if ($this->del_old) {
                $this->delete_from_entrepot($source_id, $ref);
                $this->delete_from_external_count($source_id, $ref);
        }
        $ref_exists = false;
        //Si pas de conservation ou refï¿½rence inexistante
        if (($this->del_old)||((!$this->del_old)&&(!$ref_exists))) {
            //Insertion de l'entï¿½te
            $n_header["rs"]="*";
            $n_header["ru"]="*";
            $n_header["el"]="*";
            $n_header["bl"]="m";
            $n_header["hl"]="0";
            $n_header["dt"]="a";

            //Récupération d'un ID
            $recid = $this->insert_into_external_count($source_id, $ref);

            foreach($n_header as $hc=>$code) {
                $this->insert_header_into_entrepot($source_id, $ref, $date_import, $hc, $code, $recid, $search_id);
            }
            
            $fields=[
                "titre"=>[["200","a"]],
                "contenu"=>[["327","a"]],
                "nature"=>[["900","a"],["200","e"]],
                "id_texte"=>[["001",""],["901","a"]],
                "cid"=>[["901","b"]],
                "uri"=>[["856","u"]],
                "nor"=>[["901","c"]],
                "eli"=>[["901","d"]],
                "numero"=>[["200","h"]],
            ];
   
            //Récupération du contenu
            $get=$url."/legi/getDetailsFromURI/".$record->uri;
            $curl =  new Curl();
            $result = $curl->get($get);
            if($result) {
                $result=json_decode($result);
                if ($result->result->details->content) {
                    $record->contenu=$result->result->details->content;
                }
            }
            
            //Titre des articles
            if ($record->numero) $record->titre=$record->nature." ".$record->numero." / ".$record->titre;
            
            //URL vers légifrance
            if ($record->cid) $record->uri="https://www.legifrance.gouv.fr/affichTexte.do?cidTexte=".$record->cid;
            
            foreach($record as $key=>$value) {
                for ($i=0; $i<count($fields[$key]); $i++) {
                    $ufield=$fields[$key][$i][0];
                    $usubfield=$fields[$key][$i][1];
                    $field_order=0;
                    $this->insert_content_into_entrepot($source_id, $ref, $date_import, $ufield, $usubfield, $field_order, 0, $value, $recid, $search_id);
                }
            }
            $this->rec_isbd_record($source_id, $ref, $recid);
            $this->n_recu++;
        }
    }
    
    //Fonction de recherche
    public function search($source_id,$query,$search_id) {
        global $base_path;

        $this->fetch_global_properties();
        $params=unserialize($this->parameters);
        $url=$params['url'];
        $limit=$params['limit'];
        
        if (!$limit) $limit=100;
        
        foreach($query as $amterm) {
           switch ($amterm->ufield) {
               case '010$a':
                   //Nor
                   $criterias['nor']= rawurlencode($amterm->values[0]);
                   break;
               case '200$a':
                   $criterias['titre']= rawurlencode($amterm->values[0]);
                   break;
               case 'XXX':
                   $criterias['q']= rawurlencode($amterm->values[0]);
                   break;
               case '327$a':
                   $criterias['contenu']= rawurlencode($amterm->values[0]);
                   break;
               default:
                   break;
           }
        }
        $criterias['etat']='vigueur';
        //Requête CURL au webservice...
        $get=$url."/legi/search/?";
        $c=[];
        foreach($criterias as $key=>$val) {
            $c[]=$key.'='.$val;
        }
        $get.=implode('&',$c);
        //Appel Curl
        $curl =  new Curl();
        $result = $curl->get($get);
        if ($result) {
            $result=json_decode($result);
            if (!$result->error) {
                $result=$result->result;
                //Nombre :
                $total=$result->total;
                $red=0;
                while ($red<$total) {
                    $nb=$result->nb;
                    for ($i=0; $i<$nb; $i++) {
                        $elt=$result->matches[$i];
                        if ($elt) {
                            $this->rec_record($elt,$source_id,$search_id,$url);
                        }
                        $red++;
                        if ($red>$limit) break;
                    }
                    if ($red>$limit) break;
                    if ($red<$total) {
                        $result = $curl->get($get.'&start='.$red.'&nb=10');
                        if ($result) {
                            $result=json_decode($result);
                            if ($result->error) {
                                break;
                            }
                            $result=$result->result;
                        }
                    }
                }
            }
        }     
    }
}
