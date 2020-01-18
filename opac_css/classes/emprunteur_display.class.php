<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: emprunteur_display.class.php,v 1.5.2.4 2019-11-25 12:50:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/includes/securimage/securimage.php");
//require_once ("$class_path/emprunteur_datas.class.php");

/**
 * Classe d'affichage d'un emprunteur
 * @author dbellamy
 *
 */
class emprunteur_display {


	/**
	 * Tableau d'instances de emprunteur_datas
	 * @array emprunteurs_datas
	 */
	static public $emprunteurs_datas = array();

	static private $renewal_form_field;

	static private $renewal_form_is_set;


	/**
	 * Retourne une instance de emprunteur_datas
	 * @param int $id_empr Identifiant de l'emprunteur
	 * @return emprunteur_datas
	 */
	static public function get_emprunteur_datas($id_empr) {
	    global $opac_websubscribe_valid_limit;
	    
		if (!isset(self::$emprunteurs_datas[$id_empr])) {
			self::$emprunteurs_datas[$id_empr] = new emprunteur_datas($id_empr);
			self::$emprunteurs_datas[$id_empr]->opac_websubscribe_valid_limit = $opac_websubscribe_valid_limit;
		}
		return self::$emprunteurs_datas[$id_empr];
	}

	static public function get_captcha($input_name = 'captcha_code') {
	    global $base_path, $lang;
	    
	    $_SESSION['captcha_lang'] = $lang; //on envoie la lang de l'opac à securimage

        $options = array();
        $options['input_name'] = $input_name;
        $options['securimage_path'] = $base_path . "/includes/securimage";
        $options['disable_flash_fallback'] = false;
        //$options['refresh_alt_text'] = $msg['captcha_reload'];
        //$options['refresh_title_text'] = $msg['captcha_reload'];
        $options['input_text'] = '';
        $options['show_text_input'] = 0;
        
        return  '<script type="text/javascript" src="./includes/securimage/securimage.js"></script>' . Securimage::getCaptchaHtml($options);
	}
	
	/**
	 * Ré-initialisation du singleton emprunteur_datas
	 * @param int $id_empr Identifiant de l'emprunteur
	 * @return emprunteur_datas
	 */
	static public function init_emprunteur_datas($id_empr) {
		self::$emprunteurs_datas[$id_empr] = new emprunteur_datas($id_empr);
		return self::$emprunteurs_datas[$id_empr];
	}


	static public function lookup($name, $object) {

		$return = null;
		// Si on le nom commence par empr. on va chercher les méthodes
		if (substr($name, 0, 6) == ":empr.") {

			$attributes = explode('.', $name);
			$id_empr = $object->getVariable('id_empr');

			if(!$return) {
				// On va chercher dans emprunteur_display
				$return = static::look_for_attribute_in_class("emprunteur_display", $attributes[1], array($id_empr));
			}

			if (!$return) {
				// On va chercher dans emprunteur_datas
				$emprunteur_datas = static::get_emprunteur_datas($id_empr);
				$return = static::look_for_attribute_in_class($emprunteur_datas, $attributes[1]);
			}

			// On regarde les attributs enfants recherchés
			if ($return && count($attributes) > 2) {
				for ($i = 2; $i < count($attributes); $i++) {
					// On regarde si c'est un tableau ou un objet
					if (is_array($return)) {
						$return = (isset($return[$attributes[$i]]) ? $return[$attributes[$i]] : '');
					} else if (is_object($return)) {
						$return = static::look_for_attribute_in_class($return, $attributes[$i]);
					} else {
						$return = null;
						break;
					}
				}
			}
		} else {
			$attributes = explode('.', $name);
			// On regarde si on a directement une instance d'objet, dans le cas des boucles for
			if (is_object($obj = $object->getVariable(substr($attributes[0], 1))) && (count($attributes) > 1)) {
				$return = $obj;
				for ($i = 1; $i < count($attributes); $i++) {
					// On regarde si c'est un tableau ou un objet
					if (is_array($return)) {
						$return = $return[$attributes[$i]];
					} else if (is_object($return)) {
						$return = static::look_for_attribute_in_class($return, $attributes[$i]);
					} else {
						$return = null;
						break;
					}
				}
			}
		}
		return $return;
	}


	static protected function look_for_attribute_in_class($class, $attribute, $parameters = array()) {

		if (method_exists($class, $attribute)) {
			return call_user_func_array(array($class, $attribute), $parameters);
		}
		if (method_exists($class, "get_".$attribute)) {
			return call_user_func_array(array($class, "get_".$attribute), $parameters);
		}
		if (method_exists($class, "is_".$attribute)) {
			return call_user_func_array(array($class, "is_".$attribute), $parameters);
		}
		if (is_object($class) && (isset($class->{$attribute}) || method_exists($class, '__get'))) {
			return $class->{$attribute};
		}
		return null;
	}


	static private function render($id_empr, $tpl) {
		$h2o = new H2o($tpl);
		$h2o->addLookup("emprunteur_display::lookup");
		$h2o->set(array(
		    'id_empr' => $id_empr,
		    'prefix_name' => (!empty($id_empr) ? "renewal_form_fields[empr_" : "subscribe_form_fields[empr_"),
		    'suffix_name' => (!empty($id_empr) ? "]" : "]"),
		));
		return $h2o->render();
	}

	static public function get_datas_from_post() {
	    global $subscribe_form_fields, $opac_websubscribe_valid_limit;
	    
	    $id_empr = null;
	    self::$emprunteurs_datas[$id_empr] = new emprunteur_datas($id_empr);	    
	    self::$emprunteurs_datas[$id_empr]->opac_websubscribe_valid_limit = $opac_websubscribe_valid_limit;
	    self::$emprunteurs_datas[$id_empr]->captcha =self::get_captcha();		
	    
	    $form_fields = array();
	    foreach (self::$renewal_form_field as $field) {	        
	        if ($field['display']) {
	            if(!empty($subscribe_form_fields[$field['code']])) {
	               $form_fields[$field['code']] = $subscribe_form_fields[$field['code']];
	            }
	        }
	    }	    
	    self::$emprunteurs_datas[$id_empr]->emprunteur = $form_fields;
	    return self::$emprunteurs_datas[$id_empr]->emprunteur;
	}

	/**
	 * Retourne le template
	 * @param string $template_name Nom du template : profil
	 * @param string $django_directory Répertoire Django à utiliser (paramètre opac_empr_format_django_directory par défaut)
	 * @return string Nom du template à appeler
	 */
	static public function get_template($template_name) {
		global $include_path;

		if (file_exists("$include_path/templates/empr/".$template_name."_subst.tpl.html")) {
			return "$include_path/templates/empr/".$template_name."_subst.tpl.html";
		}
		if (file_exists("$include_path/templates/empr/".$template_name.".tpl.html")) {
		    return "$include_path/templates/empr/".$template_name.".tpl.html";
		}
		return "$include_path/templates/empr/profil.tpl.html";
	}


	/**
	 * Retourne l'affichage du profil d'un emprunteur
	 * @param int $id_empr Identifiant de l'emprunteur
	 * @param string $django_directory Répertoire Django à utiliser
	 * @return string Code html d'affichage de l'emprunteur
	 */
	static public function get_display_profil($id_empr) {
		global $include_path;
		static::get_renewal_form_fields();
		
		if (!empty($id_empr)){
		static::get_emprunteur_datas($id_empr);
		} else {
    		static::get_datas_from_post();
		}
		$template_name = "profil";
		if (empty($id_empr)) {
		    $template_name = "subscribe";
		}
		$template = static::get_template($template_name);
		return static::render($id_empr, $template).
		"<script>ajax_parse_dom();</script>";
	}
	
	/**
	 * Retourne l'affichage du changement de mot de passe d'un emprunteur
	 *
	 * @param int $id_empr Identifiant de l'emprunteur
	 * @return string Code html d'affichage de changement de mot de passe emprunteur
	 */
	static public function get_display_change_password($id_empr) {
	    $template_name = "change_password";
	    if (empty($id_empr)) {
	        $template_name = "subscribe";
	    }
	    $template = static::get_template($template_name);
	    return static::render($id_empr, $template);
	}


	/**
	 * Retourne les contraintes d'affichage/modification des champs lecteurs pour le formulaire de renouvellement
	 * @return array
	 */
	static public function get_renewal_form_fields() {
		if (!isset(self::$renewal_form_field)) {
			self::$renewal_form_field = array();
			$query = "SELECT empr_renewal_form_field_code as code, empr_renewal_form_field_display as display, empr_renewal_form_field_mandatory as mandatory, empr_renewal_form_field_alterable as alterable,  empr_renewal_form_field_explanation as explanation from empr_renewal_form_fields";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				while($row = pmb_mysql_fetch_array($result, PMB_MYSQL_ASSOC)) {
					self::$renewal_form_field[$row['code']] = $row;
				}
			}
		}
		return self::$renewal_form_field;
	}

	static public function get_languages_selector($id_empr) {
		global $opac_show_languages, $include_path, $lang;

		$show_languages = $opac_show_languages[0];

		if ($show_languages == 1) {
			static::get_emprunteur_datas($id_empr);
			static::get_renewal_form_fields();
			$languages = explode(",",substr($opac_show_languages,2)) ;
			$langues = new XMLlist("$include_path/messages/languages.xml");
			$langues->analyser();
			$langs = array();
			foreach ($languages as $language) {
				$langs[$language] = $langues->table[$language];
			}
			if(empty(static::$emprunteurs_datas[$id_empr]->emprunteur['empr_lang'])) {
			    static::$emprunteurs_datas[$id_empr]->emprunteur['empr_lang'] = $lang;
			}
			$selector = "<select id='empr_lang' name='renewal_form_fields[empr_lang]' ".(self::$renewal_form_field['empr_lang']['mandatory'] ? "required " : "").
			(self::$renewal_form_field['empr_lang']['alterable'] ? "" : "readonly onclick='return false;'").">";
			foreach ($langs as $key => $l) {
			    $selector.= "<option value='$key' ".(static::$emprunteurs_datas[$id_empr]->emprunteur['empr_lang'] == $key ? "selected='selected'" : "").">$l</option>";
			}
			$selector.= "</select>";
			return $selector;
		} else return "" ;
	}

	/**
	 * Vérifie que le formulaire de renouvellement est configuré
	 * @return bool
	 */
	static public function is_renewal_form_set() {	    
	    global $empr_renewal_activate;
	    
	    if(!$empr_renewal_activate) return false;
	    
	    if(isset(static::$renewal_form_is_set)) {
			return static::$renewal_form_is_set;
		}
		$query = "select count(*) from empr_renewal_form_fields";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_result($result, 0, 0)) {
			static::$renewal_form_is_set = true;
			return true;
		}
		static::$renewal_form_is_set = false;
		return false;
	}

}
