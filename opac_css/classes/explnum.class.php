<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum.class.php,v 1.2 2015-04-03 11:16:17 jpermanne Exp $


if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classe de gestion des exemplaires numériques

if ( ! defined( 'EXPLNUM_CLASS' ) ) {
  define( 'EXPLNUM_CLASS', 1 );

	class explnum {
		
		var $explnum_id = 0;
		var $explnum_notice = 0;
		var $explnum_bulletin = 0;
		var $explnum_nom = '';
		var $explnum_mimetype = '';
		var $explnum_url = '';
		var $explnum_data = '';
		var $explnum_vignette = ''; 
		var $explnum_statut = '0';
		var $explnum_index = '';
		var $explnum_repertoire = 0;
		var $explnum_path = '';
		var $explnum_nomfichier = '';
		var $explnum_rep_nom ='';
		var $explnum_rep_path ='';
		var $explnum_index_wew ='';
		var $explnum_index_sew ='';
		var $explnum_ext ='';
		var $explnum_location = '';
		var $infos_docnum = array();
		var $params = array();
		var $unzipped_files = array();
		
		// constructeur
		function explnum($id=0, $id_notice=0, $id_bulletin=0) {
			global $dbh, $pmb_indexation_docnum_default;
			$this->unzipped_files = array();
			if ($id) {
		
				$requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_extfichier, explnum_url, explnum_data, explnum_vignette, 
				explnum_statut, explnum_index_sew, explnum_index_wew, explnum_repertoire, explnum_nomfichier, explnum_path, repertoire_nom, repertoire_path, group_concat(num_location SEPARATOR ',') as loc
				 FROM explnum left join upload_repertoire on explnum_repertoire=repertoire_id left join explnum_location on num_explnum=explnum_id where explnum_id='$id' group by explnum_id";
				$result = pmb_mysql_query($requete, $dbh);
				
				if(pmb_mysql_num_rows($result)) {
					$item = pmb_mysql_fetch_object($result);
					$this->explnum_id        = $item->explnum_id       ;
					$this->explnum_notice    = $item->explnum_notice   ;
					$this->explnum_bulletin  = $item->explnum_bulletin ;
					$this->explnum_nom       = $item->explnum_nom      ;
					$this->explnum_mimetype  = $item->explnum_mimetype ;
					$this->explnum_url       = $item->explnum_url      ;
					$this->explnum_data      = $item->explnum_data     ;
					$this->explnum_vignette  = $item->explnum_vignette ;
					$this->explnum_statut    = $item->explnum_statut ;
					$this->explnum_index_wew = $item->explnum_index_wew;
					$this->explnum_index_sew = $item->explnum_index_sew;
					$this->explnum_index     = (($item->explnum_index_wew || $item->explnum_index_sew || $pmb_indexation_docnum_default) ? 'checked' : '');
					$this->explnum_repertoire = $item->explnum_repertoire;
					$this->explnum_path = $item->explnum_path;
					$this->explnum_rep_nom = $item->repertoire_nom;
					$this->explnum_rep_path = $item->repertoire_path;
					$this->explnum_nomfichier = $item->explnum_nomfichier;
					$this->explnum_ext = $item->explnum_extfichier;
					$this->explnum_location = $item->loc ? explode(",",$item->loc) : '';
				} else { // rien trouvé en base, on va faire comme pour une création
						$req = "select repertoire_nom, repertoire_path from  upload_repertoire, users where repertoire_id=deflt_upload_repertoire and username='".SESSlogin."'";
						$res = pmb_mysql_query($req,$dbh);
						if(pmb_mysql_num_rows($res)){
							$item = pmb_mysql_fetch_object($res);
							$this->explnum_rep_nom = $item->repertoire_nom;
							$this->explnum_rep_path = $item->repertoire_path;
						} else {
							$this->explnum_rep_nom = '';
							$this->explnum_rep_path = '';
						}
						$this->explnum_id = 0;
						$this->explnum_notice = $id_notice;
						$this->explnum_bulletin = $id_bulletin;
						$this->explnum_nom = '';
						$this->explnum_mimetype = '';
						$this->explnum_url = '';
						$this->explnum_data = '';
						$this->explnum_vignette  = '' ;
						$this->explnum_statut = '0';
						$this->explnum_index = ($pmb_indexation_docnum_default ? 'checked' : '');
						$this->explnum_repertoire = 0;
						$this->explnum_path = '';
						$this->explnum_nomfichier = '';
						$this->explnum_ext = '';
						$this->explnum_location= '';
				}
				
			} else { // rien de fourni apparemment : création
				$req = "select repertoire_id, repertoire_nom, repertoire_path from  upload_repertoire, users where repertoire_id=deflt_upload_repertoire and username='".SESSlogin."'";
				$res = pmb_mysql_query($req,$dbh);
				if(pmb_mysql_num_rows($res)){
					$item = pmb_mysql_fetch_object($res);
					$this->explnum_rep_nom = $item->repertoire_nom;
					$this->explnum_rep_path = $item->repertoire_path;
					$this->explnum_repertoire = $item->repertoire_id;
				} else {
					$this->explnum_rep_nom = '';
					$this->explnum_rep_path = '';
					$this->explnum_repertoire = 0;
				}
				$this->explnum_id = $id;
				$this->explnum_notice = $id_notice;
				$this->explnum_bulletin = $id_bulletin;
				$this->explnum_nom = '';
				$this->explnum_mimetype = '';
				$this->explnum_url = '';
				$this->explnum_data = '';
				$this->explnum_vignette  = '' ;
				$this->explnum_statut = '0';
				$this->explnum_index = ($pmb_indexation_docnum_default ? 'checked' : '');;
				$this->explnum_path = '';
				$this->explnum_nomfichier='';
				$this->explnum_ext = '';
				$this->explnum_location = '';
			}
		}
		
	} # fin de la classe explnum
                                                  
} # fin de définition                             
