<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: emprunteur_datas.class.php,v 1.3.2.2 2019-11-21 09:56:16 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/parametres_perso.class.php");

/**
 * Classe qui repr�sente les donn�es d'un emprunteur
 * @author dbellamy
 *
*/
class emprunteur_datas {

	/**
	 * Identifiant de l'emprunteur
	 * @var int
	 */
	private $id;

	/**
	 * Tableau emprunteur fetch� en base
	 * @var array
	 */
	public $emprunteur;

	/**
	 * Param�tres persos
	 * @array p_perso
	 */
	private $p_perso;
	
	protected $p_perso_values;

	public function __construct($id) {
		$this->id = intval($id);
		if (!$this->id) return;
	}


	/**
	 * Charge les infos pr�sentes en base de donn�es
	 */
	private function fetch_data() {
		$query = "SELECT id_empr, empr_nom, empr_prenom, empr_adr1 ,empr_adr2, empr_cp, empr_ville, empr_pays, empr_mail, empr_lang,
				empr_tel1, empr_tel2, empr_prof, empr_year, empr_login, empr_categ, empr_codestat, empr_sexe, empr_location, empr_msg
				FROM empr WHERE id_empr='".$this->id."' ";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$this->emprunteur = pmb_mysql_fetch_assoc($result);
		}
	}


	/**
	 * Retourne les param�tres persos
	 * @return array
	 */
	public function get_p_perso() {

		if (!isset($this->p_perso)) {
			global $memo_p_perso_emprunteurs;

			if (!$memo_p_perso_emprunteurs) {
				$memo_p_perso_emprunteurs = new parametres_perso("empr");
			}
			$this->p_perso = array();
			$renewal_form_fields = emprunteur_display::get_renewal_form_fields();

			//affichage
			$ppersos = $memo_p_perso_emprunteurs->show_fields($this->id);
			if (!$this->id) {
			    $ppersos_obj = new parametres_perso('empr');
			    foreach ($ppersos_obj->t_fields as $key => $val) {
			        foreach ($ppersos['FIELDS'] as $key_pperso =>$pperso) {
			            if ($pperso['NAME'] == $val['NAME'] ) {
			                if ($val['NAME'] == 'cp_commune') {
			                }
			                $ppersos['FIELDS'][$key_pperso]['EDIT'] = $ppersos_obj->get_field_form_whith_form_value($val['idchamp']);
			            }
			        }			        
			    }	
			}
			
			//on filtre ceux qui ne sont pas visibles � l'OPAC
			if(isset($ppersos['FIELDS']) && is_array($ppersos['FIELDS']) && count($ppersos['FIELDS'])){
				foreach ($ppersos['FIELDS'] as $pperso) {
					if ($pperso['OPAC_SHOW'] ) {
						$this->p_perso[$pperso['NAME']] = $pperso;
						if (!empty($renewal_form_fields[$pperso['NAME']])) {
							$this->p_perso[$pperso['NAME']]['renewal_form_field'] = $renewal_form_fields[$pperso['NAME']];
						}
					}
				}
			}
			//edition
			$ppersos = $memo_p_perso_emprunteurs->show_editable_fields($this->id);
			//on filtre ceux qui ne sont pas visibles � l'OPAC
			if(isset($ppersos['FIELDS']) && is_array($ppersos['FIELDS']) && count($ppersos['FIELDS'])){
				foreach ($ppersos['FIELDS'] as $pperso) {
				    if ($this->id && isset($this->p_perso[$pperso['NAME']]) ) {
						$this->p_perso[$pperso['NAME']]['EDIT'] = $pperso['AFF'];
					}
				}
			}
			$this->p_perso['CHECK_SCRIPTS'] = $ppersos['CHECK_SCRIPTS'];
		}
		return $this->p_perso;
	}

	/**
	 * Retourne l'identifiant de l'emprunteur
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	public function __get($name) {
		if (!isset($this->emprunteur)) {
			$this->fetch_data();
		}
		if(is_string($name) && !empty($this->emprunteur[$name])) {
			return $this->emprunteur[$name];
		}
		return '';
	}
	
	public function set_from_form() {
	    global $renewal_form_fields, $subscribe_form_fields;
		
		$this->emprunteur = $renewal_form_fields;
		if (empty($renewal_form_fields)) {
		    $this->emprunteur = $subscribe_form_fields;
		}
		
		foreach ($this->emprunteur as $field => $value) {
		    $this->emprunteur[$field] = stripslashes($value);
		}
		//on controle les donnees postees
		$this->check_posted_empr_fields();
		
		$this->get_p_perso();
		$this->p_perso_values = array();
		foreach ($this->p_perso as $p_perso) {
			if (empty($p_perso['NAME'])) {
				continue;
			}
			global ${$p_perso['NAME']};
			if (empty(${$p_perso['NAME']})) {
			    continue;
			}
			$values = array();
			foreach (${$p_perso['NAME']} as $value) {
			    $values[]= stripslashes($value);
			}
			$this->p_perso_values[$p_perso['NAME']] = array(
					"id" => $p_perso["ID"],
					"datatype" => $p_perso['DATATYPE'],
			        "values" => $values
			);
		}
	}
	
	public function save() {
		$values = array();
		foreach ($this->emprunteur as $field => $value) {
			$values[] = "$field='".addslashes($value)."'";
		}
		pmb_mysql_query("UPDATE empr SET ".implode(",", $values)." WHERE id_empr = $this->id");
		
		foreach ($this->p_perso_values as $p_perso) {
			pmb_mysql_query("DELETE FROM empr_custom_values WHERE empr_custom_champ = ".$p_perso["id"]." AND empr_custom_origine = $this->id");
			$values = array();
			foreach ($p_perso['values'] as $value) {
				$values[]= "(".$p_perso["id"].", ".$this->id.", '".addslashes($value)."')";
			}
			pmb_mysql_query("INSERT INTO empr_custom_values (empr_custom_champ, empr_custom_origine, empr_custom_".$p_perso['datatype'].") VALUES ".implode(",", $values));
		}
	}
	
	/**
	 * on ne conserve que les champs parametres en gestion
	 */
	private function check_posted_empr_fields() {
	    if (!empty($this->emprunteur)) {
	        $cleaned_fields = [];
    	    $query = "SELECT empr_renewal_form_field_code 
                    FROM empr_renewal_form_fields 
                    WHERE empr_renewal_form_field_display = 1";
    	    $result = pmb_mysql_query($query);
    	    if (pmb_mysql_num_rows($result)) {
    	        while ($row = pmb_mysql_fetch_array($result)) {
    	            if (isset($this->emprunteur[$row[0]])) {
    	                $cleaned_fields[$row[0]] = $this->emprunteur[$row[0]];
    	            }
    	        }
    	    }
            $this->emprunteur = $cleaned_fields;
	    }
	}
}