<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onglet.inc.php,v 1.2 2015-04-03 11:16:20 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// gestion des codes statut exemplaires
?>
<script type="text/javascript">
function test_form(form)
{
	if(form.form_nom.value.length == 0)
	{
		alert("<?php echo $msg[98] ?>");
		return false;
	}
	return true;
}
</script>

<?php
function show_onglet() {
	global $msg,$dbh;
	global $charset ;

	print "<table>
	<tr>
		<th>".$msg['admin_noti_onglet_name']."</th>
	</tr>";

	// affichage du tableau des statuts
	$requete = "SELECT id_onglet, onglet_name FROM notice_onglet ORDER BY onglet_name ";
	$res = pmb_mysql_query($requete, $dbh);
	$nbr = pmb_mysql_num_rows($res);

	$parity=1;
	for($i=0;$i<$nbr;$i++) {
		$row=pmb_mysql_fetch_object($res);
		if ($parity % 2) $pair_impair = "even";else $pair_impair = "odd";
		$parity += 1;
		$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./admin.php?categ=notices&sub=onglet&action=modif&id=$row->id_onglet';\" ";
        	
		print "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'><td>".htmlentities($row->onglet_name,ENT_QUOTES, $charset)."</td>";
		print "</tr>";
	}
	print "</table>
		<input class='bouton' type='button' value=' $msg[admin_noti_onglet_ajout] ' onClick=\"document.location='./admin.php?categ=notices&sub=onglet&action=add'\" />";
}

function onglet_form($nom="", $id=0) {

	global $msg;
	global $admin_onglet_form;
	global $charset;

	$admin_onglet_form = str_replace('!!id!!', $id, $admin_onglet_form);

	if(!$id) $admin_onglet_form = str_replace('!!form_title!!', $msg[admin_noti_onglet_ajout], $admin_onglet_form);
	else $admin_onglet_form = str_replace('!!form_title!!', $msg[admin_noti_onglet_modification], $admin_onglet_form);

	$admin_onglet_form = str_replace('!!nom!!', htmlentities($nom,ENT_QUOTES, $charset), $admin_onglet_form);
	
	$admin_onglet_form = str_replace('!!nom_suppr!!', addslashes($nom), $admin_onglet_form);
	print confirmation_delete("./admin.php?categ=notices&sub=onglet&action=del&id=");
	print $admin_onglet_form;

	}

switch($action) {
	case 'update':
		if(!empty($form_nom)) {
			if($id) {
				$requete = "UPDATE notice_onglet SET onglet_name='$form_nom' WHERE id_onglet='$id' ";
				$res = pmb_mysql_query($requete, $dbh);
			} else {
				$requete = "SELECT count(1) FROM notice_onglet WHERE onglet_name='$form_nom' LIMIT 1 ";
				$res = pmb_mysql_query($requete, $dbh);
				$nbr = pmb_mysql_result($res, 0, 0);
				if($nbr == 0){
					$requete = "INSERT INTO notice_onglet (onglet_name) VALUES ('$form_nom') ";
					$res = pmb_mysql_query($requete, $dbh);
				}
			}
		}
		show_onglet();
		break;
	case 'add':
		if(empty($form_nom)) onglet_form();
			else show_onglet();
		break;
	case 'modif':
		if($id){
			$requete = "SELECT onglet_name FROM notice_onglet WHERE id_onglet='$id' ";
			$res = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				onglet_form($row->onglet_name, $id);
			} else {
				show_onglet();
			}
		} else {
			show_onglet();
		}
		break;
	case 'del':
		if ($id) {			
			$requete = "DELETE FROM notice_onglet WHERE id_onglet='$id' ";
			$res = pmb_mysql_query($requete, $dbh);
			$requete = "OPTIMIZE TABLE origine_notice ";
			$res = pmb_mysql_query($requete, $dbh);
			show_onglet();
		}
		break;
	default:
		show_onglet();
		break;
	}
