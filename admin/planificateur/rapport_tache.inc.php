<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rapport_tache.inc.php,v 1.2 2015-04-03 11:16:26 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once './classes/pdf_html.class.php';
// popup d'impression PDF pour lettre de confirmation de résa
/* reçoit : id_resa */
// la formule de politesse du bas (le signataire)
$var = "pdflettreresa_fdp";
eval ("\$fdp=\"".$$var."\";");

// le texte après la liste des ouvrages en résa
$var = "pdflettreresa_after_list";
eval ("\$after_list=\"".$$var."\";");

// la position verticale limite du texte after_liste (si >, saut de page et impression)
$var = "pdflettreresa_limite_after_list";
$limite_after_list = $$var;
		
// le texte avant la liste des ouvrges en réservation
$var = "pdflettreresa_before_list";
eval ("\$before_list=\"".$$var."\";");

// le "Madame, Monsieur," ou tout autre truc du genre "Cher adhérent,"
$var = "pdflettreresa_madame_monsieur";
eval ("\$madame_monsieur=\"".$$var."\";");

// le nombre de blocs notices à imprimer sur la première page
$var = "pdflettreresa_nb_1ere_page";
$nb_1ere_page = $$var;

// le nombre de blocs notices à imprimer sur les pages suivantes
$var = "pdflettreresa_nb_par_page";
$nb_par_page = $$var;

// la taille d'un bloc notices 
$var = "pdflettreresa_taille_bloc_expl";
$taille_bloc_expl = $$var;

// la position verticale du premier bloc notice sur la première page
$var = "pdflettreresa_debut_expl_1er_page";
$debut_expl_1er_page = $$var;

// la position verticale du premier bloc notice sur les pages suivantes
$var = "pdflettreresa_debut_expl_page";
$debut_expl_page = $$var;

// la marge gauche des pages
$var = "pdflettreresa_marge_page_gauche";
$marge_page_gauche = $$var;

// la marge droite des pages
$var = "pdflettreresa_marge_page_droite";
$marge_page_droite = $$var;

// la largeur des pages
$var = "pdflettreresa_largeur_page";
$largeur_page = $$var;

// la hauteur des pages
$var = "pdflettreresa_hauteur_page";
$hauteur_page = $$var;

// le format des pages
$var = "pdflettreresa_format_page";
$format_page = $$var;

$taille_doc=array($largeur_page,$hauteur_page);

//$ourPDF = new $fpdf($format_page, 'mm', $taille_doc);
$ourPDF = new PDF_HTML();
$ourPDF->Open();

switch($pdfdoc) {
	case "rapport_tache" :
	$query_chk = "select id_tache from taches where id_tache=".$task_id;
	$res_chk = pmb_mysql_query($query_chk, $dbh);
	
	if (pmb_mysql_num_rows($res_chk) == '1') {
		//date de génération du rapport
		$rs = pmb_mysql_query("select curdate()");
		$date_MySQL = pmb_mysql_result($rs, $row);
				
		$tasks = new taches();
		foreach ($tasks->types_taches as $type_tache) {
			if ($type_tache->id_type == $type_task_id) {
				require_once($base_path."/admin/planificateur/".$type_tache->name."/".$type_tache->name.".class.php");
				eval("\$conn=new ".$type_tache->name."(\"".$base_path."/admin/planificateur/".$type_tache->name."\");");
				$task_datas = $conn->get_report_datas($task_id);
				
				$ourPDF->addPage();
				$ourPDF->SetXY (15,8);
				$ourPDF->setFont($pmb_pdf_font, 'B', 9);
				$title = "Type : ";
				$ourPDF->setFont($pmb_pdf_font, '', 9);
				$title .= $type_tache->comment;
				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $ourPDF->GetX()), 6, $title, 0, 'L', 0);
				
				$ourPDF->SetXY (15,20);
				$header = $msg["planificateur_task_name"]." : ".stripslashes($task_datas["libelle_tache"])."\n".
						$msg["tache_date_generation"]." : ".formatdate($date_MySQL)."\n".
						$msg["tache_date_dern_exec"]." : ".formatdate($task_datas['start_at'][0])."\n".
						$msg["tache_heure_dern_exec"]." : ".$task_datas['start_at'][1]."\n".
						$msg["tache_date_fin_exec"]." : ".formatdate($task_datas['end_at'][0])."\n".
						$msg["tache_heure_fin_exec"]." : ".$task_datas['end_at'][1]."\n".
						$msg["tache_statut"]. " : ".$msg["planificateur_state_".$task_datas["status"]]." (".$task_datas["indicat_progress"]."%)\n";

//				$ourPDF->SetTextColor(92, 92, 92);
				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $ourPDF->GetX()), 6, $msg["planificateur_task_name"]." : ".stripslashes($task_datas["libelle_tache"]), 0, 'L', 0);
//				$ourPDF->SetFillColor(255, 255, 255);
				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $ourPDF->GetX()), 6, $msg["tache_date_generation"]." : ".formatdate($date_MySQL)."\n", 0, 'L', 0);
//				$ourPDF->SetDrawColor(127, 127, 127);
				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $ourPDF->GetX()), 6, $msg["tache_date_dern_exec"]." : ".formatdate($task_datas['start_at'][0])."\n", 0, 'L', 0);
//				$ourPDF->SetDrawColor(255, 255, 255);
				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $ourPDF->GetX()), 6, $msg["tache_heure_dern_exec"]." : ".$task_datas['start_at'][1]."\n", 0, 'L', 0);
//				$ourPDF->SetDrawColor(127, 127, 127);
				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $ourPDF->GetX()), 6, $msg["tache_date_fin_exec"]." : ".formatdate($task_datas['end_at'][0])."\n", 0, 'L', 0);
//				$ourPDF->SetDrawColor(255, 255, 255);
				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $ourPDF->GetX()), 6, $msg["tache_heure_fin_exec"]." : ".$task_datas['end_at'][1]."\n", 0, 'L', 0);
//				$ourPDF->SetDrawColor(127, 127, 127);
				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $ourPDF->GetX()), 6, $msg["tache_statut"]. " : ".$msg["planificateur_state_".$task_datas["status"]]." (".$task_datas["indicat_progress"]."%)\n", 0, 'L', 0);
//				$ourPDF->SetDrawColor(255, 255, 255);
				
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $ourPDF->GetX()), 6, $header, 0, 'L', 0);
				$ourPDF->SetXY (15,70);
				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $ourPDF->GetX()), 8, $msg["tache_report_execution"], 0, 'L', 0);
				
				$report_execution = $conn->show_report($task_datas["rapport"]);
//				$ourPDF = new PDF_HTML();
//				$ourPDF->AddPage();
				$ourPDF->SetFont('Arial');
				
				$ourPDF->WriteHTML($report_execution);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, $msg["planificateur_task_name"], 0, 'L', 0);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, $msg["tache_date_generation"], 0, 'L', 0);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, $msg["tache_date_dern_exec"], 0, 'L', 0);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, $msg["tache_heure_dern_exec"], 0, 'L', 0);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, $msg["tache_date_fin_exec"], 0, 'L', 0);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, $msg["tache_heure_fin_exec"], 0, 'L', 0);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, $msg["tache_statut"], 0, 'L', 0);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, $msg["tache_report_execution"], 0, 'L', 0);
			
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, $task_datas["id_tache"], 0, 'L', 0);
//				$ourPDF->SetXY (75,8);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, stripslashes($task_datas["libelle_tache"]), 0, 'L', 0);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, formatdate($date_MySQL), 0, 'L', 0);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, formatdate($task_datas['start_at'][0]), 0, 'L', 0);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, $task_datas['start_at'][1], 0, 'L', 0);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, formatdate($task_datas['end_at'][0]), 0, 'L', 0);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, $task_datas['end_at'][1], 0, 'L', 0);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, $msg["planificateur_state_".$task_datas["status"]], 0, 'L', 0);
//				$ourPDF->multiCell(($largeur_page - $marge_page_droite - $marge_page_gauche), 8, $task_datas["indicat_progress"], 0, 'L', 0);
				
			}
		}
	}
	break;
	default :
		break;
	}

if ($probleme) echo "<script> self.close(); </script>" ;
	else $ourPDF->OutPut();
