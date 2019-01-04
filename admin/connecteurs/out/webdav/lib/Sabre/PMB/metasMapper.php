<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: metasMapper.php,v 1.2 2015-10-13 08:05:28 ngantier Exp $


namespace Sabre\PMB;
use Sabre\PMB;

class metasMapper {
	protected $config;
	protected $mimetype;
	protected $name;
	protected $metas;
	protected $data;
	protected $map;
	
	public function __construct($config, $metas, $mimetype="", $name=""){
		$this->config = $config;
		$this->mimetype = $mimetype;	
		$this->name = $name;	
		$this->metas = $metas;	
		$this->map = $this->get_map();		
		$this->data=array();		
	}

	protected function get_map(){	
		global $pmb_keyword_sep;
		return [
			"meta"=>[
				"Title"=>[
					'field_type'=>'notice',
					'field'=>'tit1',
					'function'=>'concat',
					'params'=>[', ']
						
				],
				"Author"=>[
					'field_type'=>'authors',
					'field'=>'authors',
					'function'=>'',
					'params'=>[', ']
						
				],
				"Subject"=>[
					'field_type'=>'notice',
					'field'=>'tit4',
					'function'=>'concat',
					'params'=>[', ']
						
				],
				"CreateDate"=>[
					'field_type'=>'notice',
					'field'=>'year',
					'function'=>'creation_date',
					'params'=>[]
						
				],
				"PageCount"=>[
					'field_type'=>'notice',
					'field'=>'npages',
					'function'=>'concat',
					'params'=>[]
						
				],
				"Keywords"=>[
					'field_type'=>'notice',
					'field'=>'index_l',
					'function'=>'keywords',
					'params'=>[$pmb_keyword_sep]
						
				],					
			],			
		];
	}
	
	protected function concat($field,$new_field,$params){
		$sep=$params[0];
		if($field && $new_field)$field.=$sep;
		return $field.$new_field;
	}	

	protected function affecte($field,$new_field,$params){
		return $new_field;
	}

	protected function creation_date($field,$new_field,$params){
		return substr($new_field,0,4);
	}

	protected function keywords($field,$new_field,$params){
		$keywords="";
		if(count($new_field))
		foreach($new_field as $keyword){
			if($keywords != "")	$keywords.= $params[0];
			$keywords.=$keyword;
		}
		return $keywords;
	}
	
	public function get_notice_id(){
		global $pmb_keyword_sep;
		
		$notice_id = 0;
		$this->data=array();
		$this->data['tit1'] = $this->data['tit4'] = $this->data['authors'] = $this->data['co_authors'] = $this->data['code'] = $this->data['npages'] = 
		$this->data['year'] = $this->data['index_l'] = $this->data['url'] = $this->data['thumbnail_content'] = $this->data['publisher'] = $this->data['n_resume'] = "";
		
		if($this->mimetype == "application/epub+zip"){
			//pour les ebook, on gère ca directement ici !

			$this->data['tit1'] = $this->metas['title'][0];
			$this->data['authors'] = $this->metas['creator'];
			$this->data['co_authors'] = $this->metas['contributor'];
			if($this->metas['identifier']['isbn']){
				$this->data['code'] = \formatISBN($this->metas['identifier']['isbn'],13);
			}else if($this->metas['identifier']['ean']){
				$this->data['code'] = \EANtoISBN($this->metas['identifier']['ean']);
				$this->data['code'] = \formatISBN($code,13);
			}
			if($this->metas['identifier']['uri']){
				$this->data['url'] = \clean_string($this->metas['identifier']['uri']);
			}
			$this->data['publisher'] = $this->metas['publisher'][0];
			$this->data['year'] = $this->metas['date'][0]['value'];
			if(strlen($this->data['year']) && strlen($this->data['year']) != 4){
				$this->data['year'] = \formatdate(detectFormatDate($this->data['year']));
			}
			$this->data['lang']= $this->metas['language'];
			$this->data['n_resume'] = implode("\n",$this->metas['description']);
			$this->data['keywords'] = implode($pmb_keyword_sep,$this->metas['subject']);
			$this->data['thumbnail_content']=$this->metas['thumbnail_content'];
		
		}else{					
			foreach($this->map['meta'] as $map_field => $map){
				foreach($this->metas as $meta_field=>$meta_value){
					if($map_field==$meta_field){
						if(method_exists($this, $map['function'])){
							$this->data[$map['field']]=$this->$map['function']($this->data[$map['field']],$meta_value,$map['params']);			
						}else{ 
							$this->data[$map['field']]=$meta_value;
						}			
						break;
					}
				}
			}
		}
		if(!$this->data['tit1']) $this->data['tit1'] = $this->name;
		
		$notice_id=$this->create_notice();

		$notice_id=$this->dedoublonne($notice_id);
		
		return $notice_id;
	}

	protected function dedoublonne($notice_id){
		global $pmb_notice_controle_doublons;
		
		$sign = new \notice_doublon();
		$signature=$sign->gen_signature($notice_id);
		if($pmb_notice_controle_doublons){
			$q = "select notice_id from notices where signature='".$signature."' and notice_id != ".$notice_id." limit 1";
			$res = pmb_mysql_query($q);
			if (pmb_mysql_num_rows($res)) {
				$r=pmb_mysql_fetch_object($res);
				// doublon existe, on supprime la notice créée
				\notice::del_notice($notice_id);
				return $r->notice_id;
			}
		}
		pmb_mysql_query("update notices set signature = '".$signature."' where notice_id = ".$notice_id);
		return $notice_id;
	
	}
		
	protected function create_notice(){
		global $pmb_keyword_sep;
		global $pmb_type_audit;
		global $webdav_current_user_name,$webdav_current_user_id;
		
		if($this->data['publisher']){
			$ed_1 = \editeur::import(array('name'=>$this->data['publisher']));
		}else $ed_1 = 0;
			
		
		$ind_wew = $this->data['tit1']." ".$this->data['tit4'];
		$ind_sew = \strip_empty_words($ind_wew) ;
			
		$query = "insert into notices set
				tit1 = '".addslashes($this->data['tit1'])."',".
						($this->data['code'] ? "code='".$this->data['code']."',":"").
						"ed1_id = '".$ed_1."',".
						($this->data['tit4'] ? "tit4 = '".addslashes($this->data['tit4'])."'," : "").
						($this->data['npages'] ? "npages = '".addslashes($this->data['npages'])."'," : "").
						($this->data['index_l'] ? "index_l = '".addslashes($this->data['index_l'])."'," : "")."
				year = '".$this->data['year']."',
				niveau_biblio='m',
				niveau_hierar='0',
				statut = '".$this->config['default_statut']."',
				index_wew = '".$ind_wew."',
				index_sew = '".$ind_sew."',
				n_resume = '".addslashes($this->data['n_resume'])."',
				lien = '".addslashes($url)."',
				index_n_resume = '".\strip_empty_words($this->data['n_resume'])."',".
						($this->data['thumbnail_content'] ? "thumbnail_url = 'data:image/png;base64,".base64_encode($this->data['thumbnail_content'])."',":"").
						"create_date = sysdate(),
				update_date = sysdate()";
		pmb_mysql_query($query);
		$notice_id = pmb_mysql_insert_id();

		//traitement audit
		if ($pmb_type_audit) {
			$query = "INSERT INTO audit SET ";
			$query .= "type_obj='1', ";
			$query .= "object_id='$notice_id', ";
			$query .= "user_id='$webdav_current_user_id', ";
			$query .= "user_name='$webdav_current_user_name', ";
			$query .= "type_modif=1 ";
			$result = @pmb_mysql_query($query);
		}
			
		if(count($this->data['authors'])){
			$i=0;
			foreach($this->data['authors'] as $author){
				$aut = array();
				if($author['file-as']){
					$infos = explode(",",$author['file-as']);
					$aut = array(
							'name' => $infos[0],
							'rejete' => $infos[1],
							'type' => 70
					);
				}
				if(!$aut['name']){
					$aut = array(
							'name' => $author['value'],
							'type' => 70
					);
				}
				$aut_id = \auteur::import($aut);
				if($aut_id){
					$query = "insert into responsability set
							responsability_author = '".$aut_id."',
							responsability_notice = '".$notice_id."',
							responsability_type = '0'";
					pmb_mysql_query($query);
					$i++;
				}
			}
		}
		if(count($this->data['co_authors'])){
			foreach($this->data['co_authors'] as $author){
				$aut = array();
				if($author['file-as']){
					$infos = explode(",",$author['file-as']);
					$aut = array(
							'name' => $infos[0],
							'rejete' => $infos[1],
							'type' => 70
					);
				}
				if(!$aut['name']){
					$aut = array(
							'name' => $author['value'],
							'type' => 70
					);
				}
				$aut_id = \auteur::import($aut);
				if($aut_id){
					$query = "insert into responsability set
							responsability_author = '".$aut_id."',
							responsability_notice = '".$notice_id."',
							responsability_type = '0',
							repsonsability_ordre = '".$i."'";
					pmb_mysql_query($query);
					$i++;
				}
			}
		}
				
		return $notice_id;
	}

}