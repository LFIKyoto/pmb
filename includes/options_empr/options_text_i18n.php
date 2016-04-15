<?php
 // +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_text_i18n.php,v 1.1.2.2 2015-10-06 07:57:43 apetithomme Exp $

//Gestion des options de type text_i18n
$base_path = "../..";
$base_auth = "CATALOGAGE_AUTH|ADMINISTRATION_AUTH";
include ($base_path."/includes/init.inc.php");

require_once ("$include_path/parser.inc.php");
require_once ("$include_path/fields_empr.inc.php");

$options = stripslashes($options);

//Si enregistrer
if ($first == 1) {
	$param["FOR"] = "text_i18n";
	$param[SIZE][0][value] = stripslashes($SIZE*1);
	$param[MAXSIZE][0][value] = stripslashes($MAXSIZE*1);
	$param[REPEATABLE][0][value] = $REPEATABLE ? 1 : 0;
	$param[ISHTML][0][value] = $ISHTML ? 1 : 0;
	
	$options = array_to_xml($param, "OPTIONS");
	?> 
	<script>
	opener.document.formulaire.<?php  echo $name; ?>_options.value="<?php  echo str_replace("\n", "\\n", addslashes($options));?> ";
	opener.document.formulaire.<?php  echo $name; ?>_for.value="text_i18n";
	self.close();
	</script>
	<?php
	 } else {
	?> 
	<h3><?php  echo $msg[procs_options_param].$name;
	?> </h3><hr />
	
	<?php
	if (!$options) $options = "<OPTIONS></OPTIONS>";
	 $param = _parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$options, "OPTIONS");
	if ($param["FOR"] != "text_i18n") {
		$param = array();
		$param["FOR"] = "text_i18n";
	}
	//Formulaire
	?> 
	
	<form class='form-<?php echo $current_module ?>' name="formulaire" action="options_text_i18n.php" method="post">
	<h3><?php  echo $type_list_empr[$type];
	?> </h3>
	<div class='form-contenu'>
	<input type="hidden" name="first" value="1">
	<input type="hidden" name="name" value="<?php  echo htmlentities(
		$name,
		ENT_QUOTES,
		$charset);
	?>">
	<table class='table-no-border' width=100%>
	<tr><td><?php  echo $msg[procs_options_text_taille];
	?> </td><td><input class='saisie-10em' type="text" name="SIZE" value="<?php  echo htmlentities(
		$param[SIZE][0][value],
		ENT_QUOTES,
		$charset);
	?>"></td></tr>
	<tr><td><?php  echo $msg[procs_options_text_max];
	?> </td><td><input type="text" class='saisie-10em' name="MAXSIZE" value="<?php  echo htmlentities(
		$param[MAXSIZE][0][value],
		ENT_QUOTES,
		$charset);
	?>"></td></tr>
	<tr><td><?php  echo $msg[persofield_textrepeat];
	?> </td><td><input type="checkbox" name="REPEATABLE" <?php  echo $param[REPEATABLE][0][value] ? ' checked ' : "";
	?>></td></tr>
	<tr><td><?php  echo $msg[persofield_textishtml];
	?> </td><td><input type="checkbox" name="ISHTML" <?php  echo $param[ISHTML][0][value] ? ' checked ' : "";
	?>></td></tr>
	</table>
	</div>
	<input class="bouton" type="submit" value="<?php  echo $msg[77];
	?>">
	</form>
	<?php
	 }
?>
</body>
</html>