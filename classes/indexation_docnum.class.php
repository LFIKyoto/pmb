<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation_docnum.class.php,v 1.30 2015-04-03 11:16:19 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/XMLlist.class.php");
require_once("$base_path/catalog/explnum/index_docnum/index_pdf.class.php");
require_once("$base_path/catalog/explnum/index_docnum/index_html.class.php");
require_once("$base_path/catalog/explnum/index_docnum/index_txt.class.php");
require_once("$base_path/catalog/explnum/index_docnum/index_oo.class.php");
require_once("$base_path/catalog/explnum/index_docnum/index_bnf.class.php");
require_once("$base_path/catalog/explnum/index_docnum/index_mso.class.php");
require_once("$base_path/catalog/explnum/index_docnum/index_epub.class.php");
require_once("$class_path/curl.class.php");
require_once("$class_path/upload_folder.class.php");
require_once("$include_path/explnum.inc.php");

/**
 * Classe de gestion de l'indexation des documents numériques
 */
class indexation_docnum {
	
	var $id_explnum;
	var $fichier='';
	var $file_content='';
	var $file_url='';
	var $mimetype='';
	var $explnum_nomfichier='';
	var $ext='';
	var $os='';
	var $class_associee='';
	var $texte='';
	var $vignette='';
	
	/**
	 * Constructeur
	 */
	function indexation_docnum($id, $texte=''){
		$this->id_explnum = $id;
		if(!$texte){
			$this->fetch_data();
			if ($this->file_content || $this->mimetype == 'URL') {
				$this->run_index();
			}
		} else {
			$this->texte = $texte;
		}
		
	}
	
	/**
	 * Parcours des données de la table explnum
	 */
	function fetch_data(){
		global $dbh;
		
		$rqt_expl = "select explnum_mimetype, explnum_nomfichier, explnum_extfichier, explnum_data, explnum_url, concat(repertoire_path,explnum_path) as path, explnum_repertoire from explnum left join upload_repertoire on repertoire_id=explnum_repertoire where explnum_id='".$this->id_explnum."'";
		$result_expl = pmb_mysql_query($rqt_expl,$dbh);
		if($result_expl) {
			while(($explnum = pmb_mysql_fetch_object($result_expl))){
				if($explnum->explnum_data)
					//le fichier est en base
					$this->file_content = $explnum->explnum_data;
				else {
					//le fichier est en upload
					$up = new upload_folder($explnum->explnum_repertoire);
					$path = str_replace('//','/',$explnum->path.$explnum->explnum_nomfichier);
					if($path){
						$path = $up->encoder_chaine($path);
						if(file_exists($path)){
							$fp = fopen($path , "r" ) ;
							if((filesize($path)) && (filesize($path) < $this->return_bytes(ini_get('upload_max_filesize')) && (ini_get('memory_limit')*1==-1 || (filesize($path) < (($this->return_bytes(ini_get('memory_limit'))*1)-(memory_get_usage(true)*1)))))){
								$this->file_content = fread ($fp, filesize($path));
							}else{
								$this->file_content ="";
							}
							fclose ($fp) ;
						} else $this->file_content = "";
					}
				} 
				$this->file_url = $explnum->explnum_url;
				$this->mimetype = $explnum->explnum_mimetype;
				$this->explnum_nomfichier = $explnum->explnum_nomfichier;
				$this->ext = $explnum->explnum_extfichier;
			}
		}	
	}
	/**
	 * Pour avoir la taille en octets
	 */
	function return_bytes($val) {
	    $val = trim($val);
	    $last = strtolower($val[strlen($val)-1]);
	    switch($last) {
	        // Le modifieur 'G' est disponible depuis PHP 5.1.0
	        case 'g':
	            $val *= 1024;
	        case 'm':
	            $val *= 1024;
	        case 'k':
	            $val *= 1024;
	    }
	    return $val;
	}
	/**
	 * Exécution du processus d'indexation
	 */
	function run_index(){
		if($this->mimetype == 'URL'){
			//récupération par cURL
			$this->get_file_from_curl($this->file_url);
			create_tableau_mimetype();
			$this->mimetype = trouve_mimetype($this->fichier);
			if(!$this->mimetype){
				//Test sur l'extension du fichier
				$this->ext = extension_fichier($this->file_url);
				$this->mimetype = trouve_mimetype($this->file_url,$this->ext);
			}
			if(!$this->mimetype && $this->explnum_nomfichier){
				//Test sur l'extension du fichier
				$this->ext = extension_fichier($this->explnum_nomfichier);
				$this->mimetype = trouve_mimetype($this->file_url,$this->ext);
			}
			if ($this->mimetype && !$this->ext) {
				$this->ext = extension_fichier($this->file_url);
			}
			if ($this->mimetype && $this->explnum_nomfichier && !$this->ext) {
				$this->ext = extension_fichier($this->explnum_nomfichier);
			}
			if (!$this->mimetype) {
				$this->mimetype = "URL";
			}
			global $prefix_url_image ;
			if ($prefix_url_image) $tmpprefix_url_image = $prefix_url_image; 
				else $tmpprefix_url_image = "./" ;
			if($tmp = construire_vignette('',"",$this->file_url)){
				$this->vignette = $tmp;
			}else{
				$this->vignette = construire_vignette('',$tmpprefix_url_image."images/mimetype/".icone_mimetype($this->mimetype, $this->ext));
			}
		} else {
			//récupération dans la base
			$this->get_file($this->file_content);
			create_tableau_mimetype();
			if(!$this->mimetype) $this->mimetype = trouve_mimetype($this->fichier);
			if(!$this->mimetype && $this->explnum_nomfichier){
				//Test sur l'extension du fichier
				$this->ext = extension_fichier($this->explnum_nomfichier);
				$this->mimetype = trouve_mimetype($this->fichier,$this->ext);
			}
		}
		if (file_exists($this->fichier)) {
			//On parse le XML pour recupérer le nom de la classe
			$this->parse_xml();
			//On choisit la classe correspondant au traitement du type MIME
			$this->choose_class($this->class_associee);
		}
	}
	
	/**
	 * On récupère le nom de la classe de traitement en fonction du  mimetype
	 */
	function parse_xml(){
		global $base_path;
		
		$parse = new XMLlist("$base_path/catalog/explnum/index_docnum/index_doc.xml");	
		$parse->analyser();
		if($this->mimetype) {
			$class = $parse->table[$this->mimetype];
		}
		if($class) {
			$this->class_associee = $class;
		} else {
			$this->class_associee = '';
		}
	}
	
	/**
	 * On récupère le texte du document numérique grâce à la bonne classe
	 */
	function choose_class($class_name){
		if($class_name){
			$index_class = new $class_name($this->fichier,$this->mimetype,$this->ext);
			$this->texte = $index_class->get_text($this->fichier);
		}
	}
	
	/**
	 * On récupère le contenu du fichier qui est en base
	 */
	function get_file($filecontent){
		global $base_path;
		
		//On définit un nom unique dans le dossier temporaire
		$nom_temp = session_id().microtime();
		$nom_temp = str_replace(' ','_',$nom_temp);
		$nom_temp = str_replace('.','_',$nom_temp);
		
		//On écrit le contenu dans le fichier
		$fd = fopen("$base_path/temp/".$nom_temp,"w");
		fwrite($fd,$filecontent);
		fclose($fd);	
		$this->fichier = "$base_path/temp/".$nom_temp;	
	}
	
	/**
	 * On récupère le contenu du fichier à distance
	 */
	function get_file_from_curl($f_url){
		global $base_path;
		
		//On définit un nom unique dans le dossier temporaire
		$nom_temp = session_id().microtime();
		$nom_temp = str_replace(' ','_',$nom_temp);
		$nom_temp = str_replace('.','_',$nom_temp);
		$this->fichier = "$base_path/temp/".$nom_temp;
		$aCurl = new Curl();
		$aCurl->save_file_name=$this->fichier; 
		$aCurl->get($f_url);	
		
	}
	
	/**
	 * On indexe le document numérique
	 */
	function indexer(){
		global $dbh;
		
		$explnum_index_sew = strip_empty_words($this->texte);
		if(strlen($explnum_index_sew)) {
			$explnum_index_sew = ' '.$explnum_index_sew.' ';
		}
		$rqt = " update explnum set explnum_index_sew='".addslashes($explnum_index_sew)."', explnum_index_wew='".addslashes($this->texte)."' where explnum_id='".$this->id_explnum."'";
		pmb_mysql_query($rqt,$dbh);	
		if (file_exists($this->fichier)) unlink($this->fichier);	
	}
	
	/**
	 * On supprime l'index du document numérique
	 */
	function desindexer(){
		global $dbh;
		
		$rqt = " update explnum set explnum_index_sew='', explnum_index_wew='' where explnum_id='".$this->id_explnum."'";
		pmb_mysql_query($rqt,$dbh);	

	}
}
?>