<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_reader_relance_adhesion_PDF.class.php,v 1.4.2.1 2019-10-23 06:49:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/pdf/reader/lettre_reader_PDF.class.php");
require_once($class_path."/emprunteur.class.php");

class lettre_reader_relance_adhesion_PDF extends lettre_reader_PDF {
	
    protected static function get_parameter_prefix() {
        return "pdflettreadhesion";
    }
		
    protected function _init_default_parameters() {
        $this->_init_parameter_value('list_order', 'empr_nom, empr_prenom');
    }
    
	protected function _init_default_positions() {
	    $this->_init_position_values('date_jour', array($this->w/2,98,0,0,10));
		$this->_init_position_values('biblio_info', array($this->get_parameter_value('marge_page_gauche'),10));
		$this->_init_position_values('lecteur_adresse', array($this->get_parameter_value('marge_page_gauche'),45));
		$this->_init_position_values('madame_monsieur', array($this->get_parameter_value('marge_page_gauche'),125,0,0,12));
	}
	
	protected function get_query_list_order_all() {
	    return " ORDER BY ".$this->get_parameter_value('list_order');
	}
	
	protected function get_query_list_all() {
		global $pmb_lecteurs_localises, $empr_location_id, $deflt2docs_location;
		global $empr_statut_edit, $empr_categ_filter, $empr_codestat_filter;
		global $restricts;
		global $empr_relance_adhesion;
		
		// restriction localisation le cas échéant
		$restrict_localisation = "";
		if ($pmb_lecteurs_localises) {
			if ($empr_location_id=="") $empr_location_id = $deflt2docs_location ;
			if ($empr_location_id!=0) $restrict_localisation = " AND empr_location='$empr_location_id' ";
		}
		
		// filtré par un statut sélectionné
		$restrict_statut="";
		if ($empr_statut_edit) {
			if ($empr_statut_edit!=0) $restrict_statut = " AND empr_statut='$empr_statut_edit' ";
		}
		$restrict_categ = '';
		if($empr_categ_filter) {
			$restrict_categ = " AND empr_categ= '".$empr_categ_filter."' ";
		}
		$restrict_codestat = '';
		if($empr_codestat_filter) {
			$restrict_codestat = " AND empr_codestat= '".$empr_codestat_filter."' ";
		}
		$requete = "SELECT empr.id_empr, empr.empr_nom, empr.empr_prenom FROM empr ";
		$restrict_empr = " WHERE 1 ";
		$restrict_requete = $restrict_empr.$restrict_localisation.$restrict_statut.$restrict_categ.$restrict_codestat." and ".$restricts;
		$requete .= $restrict_requete;
		if ($empr_relance_adhesion==1) $requete.=" and empr_mail=''";
		$requete .= " ".$this->get_query_list_order_all();
		return $requete;
	}
	
	public function doLettre($id_empr) {
		global $action;
		global $pmb_afficher_numero_lecteur_lettres;
		
		if ($action=="print_all") {
		    $requete = $this->get_query_list_all();
			$res = @pmb_mysql_query($requete);
		
			while(($empr=pmb_mysql_fetch_object($res))) {
				$this->PDF->addPage();
		
				$this->display_date_jour();
				$this->display_biblio_info() ;
				$this->display_lecteur_adresse($empr->id_empr, 90, 0, !$pmb_afficher_numero_lecteur_lettres);
		
				$this->display_madame_monsieur($empr->id_empr);
		
				// mettre ici le texte
				$empr_temp = new emprunteur($empr->id_empr, '', FALSE, 0);
				$texte_relance = $this->get_parameter_value('texte');
				$texte_relance = str_replace("!!date_fin_adhesion!!", $empr_temp->aff_date_expiration, $texte_relance);
				$this->PDF->SetXY ($this->get_parameter_value('marge_page_gauche'),135);
				$this->PDF->multiCell($this->w, 8, $texte_relance, 0, 'J', 0);
		
				$this->PDF->multiCell($this->w, 8, $this->get_parameter_value('fdp'), 0, 'R', 0);
				
				//Ré-initialisation des positions pour les autres lettres
				$this->_init_default_positions();
			}
			pmb_mysql_free_result($res);
		} else {
			$this->PDF->addPage();
			$this->PDF->SetMargins($this->get_parameter_value('marge_page_gauche'),$this->get_parameter_value('marge_page_gauche'));
		
			$this->display_date_jour();
			$this->display_biblio_info() ;
			$this->display_lecteur_adresse($id_empr, 90, 0, !$pmb_afficher_numero_lecteur_lettres);
		
			$this->display_madame_monsieur($id_empr);
		
			// mettre ici le texte
			$empr_temp = new emprunteur($id_empr, '', FALSE, 0);
			$texte = str_replace("!!date_fin_adhesion!!", $empr_temp->aff_date_expiration, $this->get_parameter_value('texte'));
			$this->PDF->SetXY ($this->get_parameter_value('marge_page_gauche'),135);
			$this->PDF->multiCell($this->w, 8, $texte, 0, 'J', 0);
		
			$this->PDF->multiCell($this->w, 8, $this->get_parameter_value('fdp'), 0, 'R', 0);
		}
	}
	
	
}