<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: templates.class.php,v 1.3 2018-10-29 09:01:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class templates {
	
	/**
	 * Fonction de génération de champs autocomplété
	 * @param string $name
	 * @param integer $id
	 * @param integer $index
	 * @param integer $value
	 * @param string $label
	 * @param string $completion
	 */
	public static function get_input_completion($name, $id, $index, $value, $label, $completion){
		global $msg;
		$template = "
			<input type='text' completion='".$completion."' autfield='".$id."_".$index."' id='".$name."_".$index."' class='saisie-30emr' name='".$name."[".$index."][label]' data-form-name='".$name."[".$index."][label]' value=\"".$label."\" />
			<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"document.getElementById('".$name."_".$index."').value=''; document.getElementById('".$id."_".$index."').value='0'; \" />
			<input type='hidden' name='".$name."[".$index."][id]' data-form-name='".$name."[".$index."][id]' id='".$id."_".$index."' value=\"".$value."\" />
			<script type='text/javascript'>
				ajax_pack_element(document.getElementById('".$name."_".$index."'));
			</script>
		";
		return $template;
	}
	
	public static function get_button_selector($name, $what, $caller, $args_others=''){
		global $msg, $charset;
		global $base_path;
		
		$template = "
			<input type='button' class='bouton' id='".$name."' name='".$name."' value='".htmlentities($msg['parcourir'], ENT_QUOTES, $charset)."' onclick=\"openPopUp('".$base_path."/select.php?what=".$what."&caller=".$caller.$args_others."', 'selector')\" />
		";
		return $template;
	}
	
	public static function get_button_add($onclick_event=''){
		$template = "
			<input type='button' class='bouton' value='+' onclick=\"".$onclick_event."\" />
		";
		return $template;
	}
	
	public static function get_event_add_completion_field($name, $id, $completion){
		$template = "
			<script type='text/javascript'>
				function add_".$name."() {
					templates.add_completion_field('".$name."', '".$id."', '".$completion."');
				}		
			</script>
		";
		return $template;
	}
	
	public static function get_button_add_completion_field($name, $id, $completion){
		$template = "
			<input type='button' class='bouton' value='+' onclick=\"templates.add_completion_field('".$name."', '".$id."', '".$completion."');\" />
		";
		return $template;
	}
	
	public static function get_input_hidden($name, $value) {
		$template = "<input type='hidden' id='".$name."' name='".$name."' value=\"".$value."\" />";
		return $template;
	}
	
	public static function get_display_elements_completion_field($elements, $caller, $element_name, $element_id, $completion) {
		global $msg;
	
		$display = '';
		$display .= templates::get_event_add_completion_field($element_name, $element_id, $completion);
		$display .= templates::get_button_selector($caller.'_'.$element_name.'_selector', $completion, $caller, '&param1='.$element_id.'&param2='.$element_name);
		$display .= templates::get_button_add_completion_field($element_name, $element_id, $completion);
		
		if(count($elements)) {
			foreach ($elements as $i=>$element) {
				$display .= "<div id='".$caller."_".$element_name."_".$i."'>";
				$display .= templates::get_input_completion($element_name, $element_id, $i, $element['id'], $element['name'], $completion);
				$display .= "</div>";
			}
			$display .= templates::get_input_hidden('max_'.$element_name, count($elements));
		} else {
			$display .= "<div id='".$caller."_".$element_name."_0'>";
			$display .= templates::get_input_completion($element_name, $element_id, 0, '', '', $completion);
			$display .= "</div>";
			$display .= templates::get_input_hidden('max_'.$element_name, 1);
		}
		$display.= "<div id='add".$element_name."'></div>";
		return $display;
	}
	
}