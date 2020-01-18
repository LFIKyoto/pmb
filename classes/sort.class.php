<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sort.class.php,v 1.60.2.4 2019-11-20 08:57:06 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($include_path.'/parser.inc.php');
require_once ($include_path.'/templates/sort.tpl.php');
require_once ($class_path.'/parametres_perso.class.php');

/**
 * Classe d'abstraction d'acces aux donn�es des tris stock�s
 * l'admin utilise la base et l'opac les sessions 
 */
class dataSort {

	public $typeData; //base ou session
	public $sortName;
	
	//pour le parcours des tris
	public $tabParcours;
	public $posParcours;

	public function __construct($sName,$tData) {
		$this->sortName = $sName;
		$this->typeData = $tData;
	}
	
	/**
	 * Retourne un tableau avec le nom du tri et la construction du tri
	 */
	public function recupTriParId($id) {
		switch($this->typeData) {
			case 'base':
				$result = pmb_mysql_query("SELECT nom_tri, tri_par FROM tris WHERE id_tri=" . $id);
				if ($result) {
					$tab = pmb_mysql_fetch_assoc($result);
					pmb_mysql_free_result($result);
					return $tab;
				} else
					return null;
				break;
			case 'session':
				if(isset($_SESSION["sortname".$this->sortName.$id]) && $_SESSION["sortname".$this->sortName.$id]){
					$tab["nom_tri"] = $_SESSION["sortname".$this->sortName.$id];
					$tab["tri_par"] = $_SESSION["sort".$this->sortName.$id];
				} else {
					$tab["nom_tri"] = "";
					$tab["tri_par"] = "";
				}
				return $tab;
				break;
		}
	}
	

	/**
	 * Pour initialiser un parcours des tris
	 * Retourne le nombre de tris
	 */
	public function initParcoursTris($objSort) {
		//on initialise la position du parcours
		$this->posParcours = 0;
		$this->nbResult=0;
		$this->tabParcours=null;
		
		switch($this->typeData) {
			
			case 'base':
				$result = pmb_mysql_query("SELECT id_tri, nom_tri, tri_par FROM tris WHERE tri_reference='" . $this->sortName . "' ORDER BY nom_tri;");
				//echo "SELECT id_tri, nom_tri, tri_par FROM tris WHERE tri_reference='" . $this->sortName . "' ORDER BY nom_tri<br />";
				if ($result) {
					
					//on charge les tris dans un tableau
					while ($this->tabParcours[$this->nbResult] = pmb_mysql_fetch_assoc($result)) {
						$this->nbResult++;
					}
					pmb_mysql_free_result($result);
					
					//s'il n'y a pas de tris
					if ($this->nbResult==0) {
						//on vide la session stockant le tri en cours
						$_SESSION["tri"] = "";
					}
					
					return $this->nbResult;
				} else {
					$_SESSION["tri"] = "";
					return 0;
				}
				break;
			
			case 'session':
				$this->nbResult = $_SESSION["nb_sort".$this->sortName];

				//s'il n'y a pas de tris
				if ($this->nbResult==0) {
					//on vide la session stockant le tri en cours
					$_SESSION["last_sort".$this->sortName]="";
				} else {
					//on charge les tris dans un tableau
					for($i=0; $i<$this->nbResult; $i++) {
						$this->tabParcours[$i]["id_tri"] = $i;
						$this->tabParcours[$i]["nom_tri"] = $objSort->descriptionTri($_SESSION["sort".$this->sortName.$i]);
						$this->tabParcours[$i]["tri_par"] = $_SESSION["sort".$this->sortName.$i];
					}
				}
				return $this->nbResult;
				break;
		}
		
	}
	
	/**
	 * Renvoi le tri suivant dans un parcours
	 */
	public function parcoursTriSuivant() {
		switch($this->typeData) {
			case 'base':
				$result = (isset($this->tabParcours[$this->posParcours]) ? $this->tabParcours[$this->posParcours] : '');
				$this->posParcours++;
				return $result;
				break;
			case 'session':
				$result = (isset($this->tabParcours[$this->posParcours]) ? $this->tabParcours[$this->posParcours] : '');
				$this->posParcours++;
				return $result;
				break;
		}
	}
	
	/**
	 * Enregistre un tri
	 */
	public function enregistreTri($id,$nomTri,$desTri) {
		global $msg;
		global $charset;

		switch($this->typeData) {
			case 'base':

				//$criteres = implode(",",$desTri);
				$txt_requete = "";
				
				//modif ou insertion ?
				if ($id != "") {
					//on modifie le tri avec les nouveaux criteres
					$txt_requete = "UPDATE tris SET nom_tri='" . addslashes($nomTri) . "', tri_par='" . $desTri . "' ";
					$txt_requete .= "WHERE id_tri='" . $id . "'";
				} else {
					//on v�rifie que le nom de tri n'existe pas
					$txt_requete = "SELECT id_tri FROM tris WHERE nom_tri='" . addslashes($nomTri) . "'";
					$txt_requete .= " AND tri_reference='" . $this->sortName . "'";
					if (pmb_mysql_num_rows(pmb_mysql_query($txt_requete)) == 0) {
						//on genere la requete d'insertion
						$txt_requete = "INSERT INTO tris (id_tri, tri_reference, nom_tri, tri_par) ";
						$txt_requete .= "VALUES ('','" . $this->sortName . "','" . addslashes($nomTri) . "','" . $desTri . "')";
					} else {
						//le nom existe : on le dit
						return "<script>alert ('" . $msg['tri_existant'] . "');history.back();</script>";
					}
				}
				
				if ($txt_requete!="") {
					
					//execution de la requete de modif ou d'insertion
					$requete = pmb_mysql_query($txt_requete);
					
					if (!$requete) {
						// il y a eu une erreur d'execution de la requete
						return "Erreur mysql : " . $txt_requete . "<br />" . pmb_mysql_error();
					} else
						return "";
				} else
					return "";

				break;

			case 'session':
				//si nombre de tris enregistr�s dans la session n'est pas null, parcours des variables de session pour l'existence tri et sauvegarde
				if ($_SESSION["nb_sort".$this->sortName]<=0) {
					$_SESSION["sort".$this->sortName."0"] = htmlentities($desTri,ENT_QUOTES,$charset);
					$_SESSION["nb_sort".$this->sortName] = 1;
				} else {
					$bool=false;
					for ($i=0;$i<$_SESSION["nb_sort".$this->sortName];$i++) {
						if ($_SESSION["sort".$this->sortName.$i] == htmlentities($desTri,ENT_QUOTES,$charset)) {	
							$bool=true;
						}
					}
					if ($bool==true) {
						return "<script>alert ('".$msg['tri_existant']."');</script>";
					} else {
						$_SESSION["sort".$this->sortName.$_SESSION["nb_sort".$this->sortName]] = htmlentities($desTri,ENT_QUOTES,$charset);
						$_SESSION["nb_sort".$this->sortName]++;
						return "";
					}		
				}
				break;

		}
	}
	
	/**
	 * Supprime un tri
	 */
	public function supprimeTri($idTri) {
		switch($this->typeData) {
			case 'base':
				// on v�rifie que le tri existe bien
				if (pmb_mysql_num_rows(pmb_mysql_query("SELECT * FROM tris WHERE id_tri='" . $idTri . "'")) > 0) {
					//c'est ok on supprime
					$result = pmb_mysql_query("DELETE FROM tris WHERE id_tri='" . $idTri . "'");
					if (!$result) {
						print "Erreur mysql : " . pmb_mysql_error();
					}
				}
				break;
			case 'session':
				//on charge les tris dans un tableau sauf celui � supprimer
				$posTab = 0;
				for($i=0; $i<$_SESSION['nb_sort'.$this->sortName]; $i++) {
					if ($i != $idTri) {
						//ce n'est pas le tri a supprimer
						//on le stocke dans le tableau
						$tabParcours[$posTab] = $_SESSION["sort".$this->sortName.$i];
						$posTab++;
					}
				}
				
				//on rempli les variables session sans l'element a supprimer
				for($i=0; $i<$posTab; $i++) {
					$_SESSION['sort'.$this->sortName.$i] = $tabParcours[$i];
				}
				$_SESSION['nb_sort'.$this->sortName]--;
				
				//si il ne subsiste plus d'historique de tris, mise � null des variables de session
				if ($_SESSION['nb_sort'.$this->sortName]==0) {
					$_SESSION['last_sort'.$this->sortName]="";
				}
				break;
		}
	}
	
	public function applyTri($id) {
	    if(($id) && !(isset($_GET["sort"]))) {
	        //Le tri est d�fini en gestion, on l'ajoute aux tris dispos en OPAC si n�cessaire
	        $res_tri = pmb_mysql_query("SELECT * FROM tris WHERE id_tri=".$id);
	        if (pmb_mysql_num_rows($res_tri)) {
	            $last = "";
	            $row_tri = pmb_mysql_fetch_object($res_tri);
	            if ($_SESSION["nb_sort".$this->sortName]<=0) {
	                $_SESSION["sort".$this->sortName.$_SESSION["nb_sort".$this->sortName]]=$row_tri->tri_par;
	                if ($row_tri->nom_tri) {
	                    $_SESSION["sortname".$this->sortName.$_SESSION["nb_sort".$this->sortName]]=$row_tri->nom_tri;
	                }
	                $last = 0;
	                $_SESSION["nb_sort".$this->sortName]++;
	            } else {
	                $bool=false;
	                for ($i=0;$i<$_SESSION["nb_sort".$this->sortName];$i++) {
	                    if ($_SESSION["sort".$this->sortName.$i] == $row_tri->tri_par) {
	                        $bool=true;
	                        $last = $i;
	                    }
	                }
	                if (!$bool) {
	                    $_SESSION["sort".$this->sortName.$_SESSION["nb_sort".$this->sortName]] = $row_tri->tri_par;
	                    if ($row_tri->nom_tri) {
	                        $_SESSION["sortname".$this->sortName.$_SESSION["nb_sort".$this->sortName]] = $row_tri->nom_tri;
	                    }
	                    $last = $_SESSION["nb_sort".$this->sortName];
	                    $_SESSION["nb_sort".$this->sortName]++;
	                }
	            }
	            $_SESSION["last_sort".$this->sortName]="$last";
	        }
	    }elseif(isset($_GET["sort"])){
	        $_SESSION["last_sort".$this->sortName]=$_GET["sort"];
	    }
	}
}



/**
 * Classe de tri des r�sultats de recherche dans le catalogue
 * Utilise une variable de session("tri") pour stocker le tri en cours
 * 
 */
class sort {
	public $params;
	public $error = false;
	public $error_message = "";
	public $table_tri_tempo = "tri_tempo"; //table temporaire � utiliser
	public $table_primary_tri_tempo; //Cl� primaire de la table temporaire � cr�er
	public $limit; //limitation des enregistrements � utiliser dans la requ�te de tri pour le pager
	public $champs_select; //champs �ventuels � retourner dans la requ�te
	public $table_select; //table �ventuelle � retourner dans la requ�te
	public $table_primary_key_select; //cl� de la table �ventuelle � retourner dans la requ�te
	public $dSort; // objet d'acces aux informations 
	private static $nb_instance = 1;


	/**
	 * Applique le tri donn�
	 * @$sort_name nom du tri � appliquer
	 */
	public function __construct($sort_name, $accesTri) {
		if ($sort_name) {
			$sname = $sort_name;
		} else {
			$sname = 'notices';
		}
		$this->table_tri_tempo .= "_".self::$nb_instance;
		self::$nb_instance++;

		if ($accesTri) {
			$this->dSort = new dataSort($sname,$accesTri);
		} else {
			$this->dSort = new dataSort($sname,'base');
		}
		//on charge le fichier XML
		$this->parse();
	}

	/**
	 * Affiche l'�cran de choix des tris enregistr�s
	 */
	public function show_tris_form() {
		global $show_tris_form;
		global $ligne_tableau_tris;
		global $ligne_tableau_tris_etagere;
		global $ligne_tableau_tris_rss_flux;
		global $msg,$charset;

		if ($this->dSort->initParcoursTris($this) == 0 ) { 
			//il n'y a pas de tris enregistr�s
			
			//on renvoie un message pour le dire
			$tris = $msg['aucun_tri'];
			
		} else {
			// creation du tableau de la liste des tris enregistr�s
			$parity = 1;
			$tris = "";

			//affichage des enregistrements de tris possibles
			while ($result = $this->dSort->parcoursTriSuivant()) {
				//gestion du surlignage une ligne sur 2 
				if ($parity % 2)
					$pair_impair = "even";
				else
					$pair_impair = "odd";
				
				//html d'une ligne
				if($this->caller == "etagere"){
					$tristemp = str_replace("!!id_tri!!", $result['id_tri'], $ligne_tableau_tris_etagere);	
					
				}elseif($this->caller == "rss_flux"){
				    $tristemp = str_replace("!!id_tri!!", $result['id_tri'], $ligne_tableau_tris_rss_flux);
				    
				}else {
					$tristemp = str_replace("!!id_tri!!", $result['id_tri'], $ligne_tableau_tris);
				}
				$tristemp = str_replace("!!descname_tri!!",addslashes(html_entity_decode($this->descriptionTriParId($result['id_tri']),ENT_QUOTES,$charset)), $tristemp);
				$tristemp = str_replace("!!nom_tri!!", $result['nom_tri'], $tristemp);
				$tristemp = str_replace("!!caller!!", $this->caller, $tristemp);
				$tristemp = str_replace("!!pair_impair!!", $pair_impair, $tristemp);
				$tris .= $tristemp;
				
				$parity += 1;
			}
		}
		$tris_form = str_replace("!!caller!!", $this->caller, $show_tris_form);
		switch ($this->caller){
			case "etagere" :
			case "rss_flux" :
				$callback="parent.document.getElementById('history').style.display='none';parent.window.getSort(0,''); return false;";
				break;
			default :
				$callback="parent.document.getElementById('history').style.display='none';parent.location.href='./recall.php?current=".$_SESSION["CURRENT"]."&t=NOTI&tri=-1'; return false;";
				break;
		}
		$tris_form = str_replace("!!callback!!", $callback, $tris_form);
		
		//on remplace dans le template les informations issues de la base
		$tris_form = str_replace("!!sortname!!", $this->dSort->sortName, $tris_form);
		$tris_form = str_replace("!!liste_tris!!", $tris, $tris_form);
		return $tris_form;
	}


	/**
	 * Affiche l'�cran de s�lection des criteres de tri
	 */
	public function show_sel_form($id_tri=0) {
		switch($this->dSort->typeData) {
			case 'base':
				return $this->show_sel_formAdmin($id_tri);
				break;
			case 'session':
				return $this->show_sel_formOPAC($id_tri);
				break;
		}
	}

	/**
	 * Fonction de calcul de la visibilit� d'un crit�re de tri
	 */
	public function visibility($field) {
		$visibility=true;
		if (isset($field["VAR"]) && $field["VAR"]) {
			for ($i=0; $i<count($field["VAR"]); $i++) {
				$name=$field["VAR"][$i]["NAME"] ;
				global ${$name};
				if ($field["VAR"][$i]["VISIBILITY"]=="yes") {
					$visibility=true;
				} else {
					$visibility=false;
				}
				if (isset(${$name})) {
					for ($j=0; $j<count($field["VAR"][$i]["VALUE"]); $j++) {
						if (${$name} == $field["VAR"][$i]["VALUE"][$j]["value"]) {
							if ($field["VAR"][$i]["VALUE"][$j]["VISIBILITY"]=="yes") {
								$visibility=true;
							} else {
								$visibility=false;
							}
						}
					}
				}
			}
		}
		return $visibility;
	}

	protected function _compare_labels($a, $b) {
	    global $msg;
	    
	    if(!empty($a["LABEL"])) $cmp_a = $a["LABEL"];
	    else $cmp_a = $msg[$a["NAME"]];
	    if(!empty($b["LABEL"])) $cmp_b = $b["LABEL"];
	    else $cmp_b = $msg[$b["NAME"]];
	    return strcmp(strtolower(convert_diacrit($cmp_a)), strtolower(convert_diacrit($cmp_b)));
	}
	
	protected function _sort_fields($fields) {
	    usort($fields, array($this, '_compare_labels'));
	    return $fields;
	}
	
	/**
	 * Affiche l'�cran de s�lection des criteres de tri
	 */
	public function show_sel_formAdmin($id_tri) {
		global $show_sel_form;
		global $charset;
		global $msg;

		//les champs de tris possible
		$fields = $this->params["FIELD"];
        $fields = $this->_sort_fields($fields);
        
		//initialisation des variables
		$liste_selectionnes = "";
		$nom_du_tri = "";

		//g�n�ration de la liste des criteres
		$liste_criteres = "";

		//si id_tri est renseign�, c'est alors une modification du tri s�lectionn�
		if ($id_tri!=0) {
			$result = $this->dSort->recupTriParId($id_tri);
			//$requete = pmb_mysql_query("SELECT nom_tri, tri_par FROM tris WHERE id_tri='" . $id_tri . "'");
			if ($result) {
				//$result = pmb_mysql_fetch_array($requete);
				$nom_du_tri = $result['nom_tri'];

				//recherche et d�composition du tri
				$tri_par = explode(",", $result['tri_par']);

				for ($i = 0; $i < count($tri_par); $i++) {
					
					//on decompose la description du critere de tri (c_num_2)
					$tri_par1 = explode("_", $tri_par[$i]);
					
					for ($j = 0; $j < count($fields); $j++) {
						//on parcours tous les champs (pour r�cuperer le nom)
						if ($fields[$j]["ID"] == $tri_par1[2]) {
							//on est dans le bon champs
							
							//on determine le type et le sens du tri pour l'affichage							
							switch ($tri_par1[1]) {
								case 'num' :
									if ($tri_par1[0] == "c")
										$debut = "0-9 ";
									else
										$debut = "9-0 ";
									break;
								case 'text' :
									if ($tri_par1[0] == "c")
										$debut = "A-Z ";
									else
										$debut = "Z-A ";
									break;
							}

							//la liste des champs s�lectionn�s
							$liste_selectionnes .= "<option value='" . $tri_par1[0] . "_" . $tri_par1[1] . "_" . $tri_par1[2] . "'>";
							//si champ perso, on a d�j� le libell�
							if(isset($fields[$j]['SOURCE']) && $fields[$j]['SOURCE'] == "cp") $name = $fields[$j]['LABEL'];
							else $name = $msg[$fields[$j]['NAME']]; 
							$liste_selectionnes .= $debut . "" . htmlentities($name, ENT_QUOTES, $charset);
							$liste_selectionnes .= "</option>\n";
							
							//ce champ est utilise donc on ne l'affichera pas
							$fields[$j]["UTILISE"] = true;
						
						}
					}
				}

				//on cr�� la liste des criteres restants
				for ($j = 0; $j < count($fields); $j++) {
					// sans les champs d�ja utilis�s
					if ($fields[$j]["UTILISE"]!=true){
						if ($this->visibility($fields[$j])) {
							//si champ perso, on a d�j� le libell�
							if(isset($fields[$j]['SOURCE']) && $fields[$j]['SOURCE'] == "cp") $name = $fields[$j]['LABEL'];
							else $name = $msg[$fields[$j]['NAME']]; 
							$liste_criteres .= "<option value='c_" . $fields[$j]["TYPE"] . "_" . $fields[$j]["ID"] . "'>" . htmlentities($name, ENT_QUOTES, $charset) . "</option>\n"; 
						}
					}
						
				}
				
			}
		} else {
			//on cr�� la liste des criteres
			for ($j = 0; $j < count($fields); $j++) {
				if ($this->visibility($fields[$j])) {
					//si champ perso, on a d�j� le libell�
					if(isset($fields[$j]['SOURCE']) && $fields[$j]['SOURCE'] == "cp") $name = $fields[$j]['LABEL'];
					else $name = $msg[$fields[$j]['NAME']]; 
					$liste_criteres .= "<option value='c_" . $fields[$j]["TYPE"] . "_" . $fields[$j]["ID"] . "'>" . htmlentities($name, ENT_QUOTES, $charset) . "</option>\n";
				}
			}
		}

		//on remplace toutes les variables dans le template
		$sel_form = str_replace("!!id_tri!!", $id_tri, $show_sel_form);
		$sel_form = str_replace("!!caller!!", $this->caller, $sel_form);
		$sel_form = str_replace("!!sortname!!", $this->dSort->sortName, $sel_form);
		$sel_form = str_replace("!!nom_tri!!", $nom_du_tri, $sel_form);
		$sel_form = str_replace("!!liste_criteres!!", $liste_criteres, $sel_form);
		$sel_form = str_replace("!!liste_selectionnes!!", $liste_selectionnes, $sel_form);
		
		return $sel_form;
	}

    public function show_sel_formOPAC() {
    	global $show_sel_form;
		global $liste_criteres_tri;
		global $charset;
		global $msg;
		global $opac_nb_max_criteres_tri;

		$fields = $this->params["FIELD"];
		$fields = $this->_sort_fields($fields);
		
		$liste_criteres = '';
    	for ($i=0;$i<count($fields);$i++) {
    		if ($this->visibility($fields[$i])) {
    			$liste_criteres.="<option value='".$fields[$i]["ID"]."'>".htmlentities($msg[$fields[$i]["NAME"]],ENT_QUOTES,$charset)."</option>\n";
    		}
    	}
    	
    	$listes_tri = "";
    	for ($i=1;$i<$opac_nb_max_criteres_tri;$i++) {
    		$listes_tri .= str_replace("!!idLigne!!",$i,$liste_criteres_tri);
    	}

    	$sel_form = str_replace("!!liste_criteres_tri!!", $listes_tri, $show_sel_form);
    	$sel_form = str_replace("!!liste_criteres!!", $liste_criteres, $sel_form);

    	return $sel_form;
    }

	/**
	 * Enregistre les criteres de tri dans la table tris
	 */
	public function sauvegarder($id_tri, $nom_tri, $tris_par) {
		global $msg;
		global $charset;

		$criteres = implode(",",$tris_par);

		return $this->dSort->enregistreTri($id_tri,$nom_tri,$criteres);

	}

	/**
	 * Supprime un tri sauvegarder
	 */
	public function supprimer($id_tri) {
		return $this->dSort->supprimeTri($id_tri);
	}
	
	/**
	 * Retourne le texte de description du tri � partir de sa description
	 */
	public function descriptionTri($desTri) {
		global $msg;

		//r�cuperations des champs
		$fields = $this->params["FIELD"];
		
		$tris_par = explode(",",$desTri);
		
		$trier_par_texte = "";
		foreach ($tris_par as $selectValue) {
			//d�coupage du champ (ex : c_num_2 (croissance ou d�croissance (c ou d),
			//type de champ (num,text,...) et id du champ)
			$temp = explode("_", $selectValue);

			//on genere le texte descriptif � afficher
			for ($i = 0; $i < count($fields); $i++) {
				if (isset($temp[2]) && $fields[$i]["ID"] == $temp[2]) {
					$trier_par_texte .= $msg[$fields[$i]["NAME"]] . " ";
					if ($temp[0] == "c") {
						$trier_par_texte .= $msg["tri_texte_croissant"];
					} else {
						$trier_par_texte .= $msg["tri_texte_decroissant"];
					}
					$trier_par_texte .= ",";
				}
			}
		}
		//on enleve la derniere virgule et on ajoute la )
		$trier_par_texte = substr($trier_par_texte, 0, strlen($trier_par_texte) - 1);
		
		return $trier_par_texte;
	}
	
	
	/**
	 * Retourne le texte de description du tri a partir d'un id
	 */
	public function descriptionTriParId($id_tri) {
		global $msg,$charset;

		if ($id_tri!="") {
			
			//r�cup�ration de la description du tri
			$result = $this->dSort->recupTriParId($id_tri);

			$tris_par = explode(",",$result['tri_par']);
			$nom_tri = $result['nom_tri'];
			
			$trier_par_texte = "(" . htmlentities($this->descriptionTri($result['tri_par']),ENT_QUOTES,$charset) . ")";
			
			//on concatene le message complet
			$trier_par_texte = $nom_tri." ".$trier_par_texte;
		
			return $trier_par_texte;
		} else
			return "";
				
	}
	
	public function ajoutTriForUniqueRender($trier_par) {
	    switch ($this->dSort->sortName) {
	        case 'notices':
	            if(!in_array('c_text_1', $trier_par)) {
	                $trier_par[] = 'c_text_1';
	            }
	            break;
	    }
	    return $trier_par;
	}
	
	/**
	 * Applique le tri s�lectionner
	 * Renvoi la requete finale utilisant les criteres de tri
	 */
	public function appliquer_tri($idTri_orTri, $selectTempo, $nomColonneIndex,$debLimit,$nbLimit) {
		global $msg;

		//r�cuperations des champs
		$fields = $this->params["FIELD"];
		$tableEnCours = $this->table_tri_tempo;

		//creation de la table de tri
		//$cmd_table = "DROP TABLE " . $tableEnCours;
		//pmb_mysql_query($cmd_table);
		//$cmd_table = "CREATE TABLE " . $tableEnCours . " ENGINE=MyISAM (".$selectTempo.")";
		$cmd_table = "CREATE TEMPORARY TABLE " . $tableEnCours . " ENGINE=MyISAM (".$selectTempo.")";
		pmb_mysql_query($cmd_table);
		$cmd_table = "ALTER TABLE " . $tableEnCours . " ADD PRIMARY KEY (" . $nomColonneIndex.")";
		pmb_mysql_query($cmd_table);	

		//r�cup�ration de la description du tri
		$result = [];
		if (is_array($idTri_orTri)) {
			$result = $idTri_orTri;
		}
		else {
			$result = $this->dSort->recupTriParId($idTri_orTri);
		}
		$trier_par = explode(",",$result['tri_par']);
		$trier_par = $this->ajoutTriForUniqueRender($trier_par);
		
		//parcours des champs sur lesquels trier
		$orderby = '';
		for ($j = 0; $j < count($trier_par); $j++) {
			//d�coupage du champ (ex : c_num_2 (croissance ou d�croissance (c ou d),
			//type de champ (num,text,...) et id du champ)
			$temp = explode("_", $trier_par[$j]);

			//on parcours tous les champs de tri possible
			for ($i = 0; $i < count($fields); $i++) {

				//afin de trouver ceux sur lesquels le tri s'applique
				if (isset($temp[2]) && $fields[$i]["ID"] == $temp[2]) {
					//on est sur un champ de tri

					//suivant le type de champ
					switch ($fields[$i]["TYPEFIELD"]) {
						case "internal":
							//c'est un champ de la requete de base
							
							//on verifie que le champ est dans la table temporaire
							$requete_fields = pmb_mysql_query("SELECT * FROM " . $tableEnCours . " LIMIT 1");
							$x = 0;
							while ($x < pmb_mysql_num_fields($requete_fields)) {
								$ligne = pmb_mysql_fetch_field($requete_fields, $x);
								if ($ligne->name == $fields[$i]["TABLEFIELD"][0]['value']) {
									//le champ est la donc on ajoute le champ au order
									$orderby .= $this->ajoutOrder($fields[$i]["TABLEFIELD"][0]['value'],$temp[0]) . ",";
									$x = pmb_mysql_num_fields($requete_fields);
								}
								$x++;
							}
							pmb_mysql_free_result($requete_fields);
							break;

						case "select":
							//une requete union est n�c�ssaire
							
							//le nom du champ on ajoute tb pour corriger le probleme des noms numeriques
							$nomChamp = "tb".$fields[$i]["NAME"];
							
							//on ajoute la colonne au orderby
							$orderby .= $this->ajoutOrder($nomChamp,$temp[0]) . ",";
													
							//on ajoute la colonne � la table temporaire
							$this->ajoutColonneTableTempo($tableEnCours, $nomChamp, $temp[1]);

							
							//on parcours la ou les tables pour generer les updates
							for ($x = 0; $x < count($fields[$i]["TABLE"]); $x++) {
								
								$requete = $this->genereRequeteUpdate($fields[$i]["TABLE"][$x], $tableEnCours, $nomChamp, $nomColonneIndex);
								
								//echo("updateSort:".$requete."<br />");
								pmb_mysql_query($requete);
							}
							
							//on a aussi des champs persos maitenant...
							if(isset($fields[$i]['SOURCE']) && $fields[$i]['SOURCE'] == "cp"){
								$requete = $this->generateRequeteCPUpdate($fields[$i], $tableEnCours, $nomChamp);
								pmb_mysql_query($requete);
							}
							
							break;
						case "authority":
						    //le nom du champ on ajoute tb pour corriger le probleme des noms numeriques
						    $nomChamp = "tb".$fields[$i]["NAME"];
						    
						    //on ajoute la colonne au orderby
						    $orderby .= $this->ajoutOrder($nomChamp,$temp[0]) . ",";
						    
						    //on ajoute la colonne � la table temporaire
						    $this->ajoutColonneTableTempo($tableEnCours, $nomChamp, $temp[1]);
						    
						    //on a aussi des champs persos maitenant...
						    if(isset($fields[$i]['SOURCE']) && $fields[$i]['SOURCE'] == "cp"){
						        $requete = $this->generateRequeteCPAuthorityUpdate($fields[$i], $tableEnCours, $nomChamp);
						        pmb_mysql_query($requete);
						    }
						    break;

					} //switch
				} //if ($fields[$i]["ID"] == $temp[2]) {
			} //for ($i = 0; $i < count($fields); $i++) {
		} //for ($j = 0; $j < count($trier_par); $j++) {
		
		if ($orderby!="") {
			//on enleve la derniere virgule
			$orderby = substr($orderby, 0, strlen($orderby) - 1);

			//on va classer la table tempo suivant les criteres donn�s
			$requete = "ALTER TABLE " . $tableEnCours ." ORDER BY ". $orderby;
			pmb_mysql_query($requete);
		}

		//on retourne la requete sur la table de tri
    	if ($this->table_select!="") {
    		//c'est une requete avec des informations ext�rieures
    		$requete = "SELECT " . $nomColonneIndex . "," . $this->champs_select;
    		$requete .= " FROM " . $this->table_tri_tempo . "," . $this->table_select;
    		$requete .= " WHERE " . $this->table_select . "." . $this->table_primary_key_select;
    		$requete .= "=" . $this->table_tri_tempo . "." . $nomColonneIndex;
    		$requete .= " GROUP BY " . $nomColonneIndex;	
    		if ($orderby!="") $requete .= " ORDER BY " . $orderby;
    		if ($nbLimit>0) $requete .= " LIMIT " . $debLimit . "," . $nbLimit;
    	} else {
			if ($nbLimit>0) {
	    		//requete de base sur la table tri�e avec limit
				$requete = "SELECT * FROM " . $tableEnCours;
				if ($orderby!="") $requete .= " ORDER BY " . $orderby;
				$requete .= " LIMIT " . $debLimit . "," . $nbLimit; 
			} else {
	    		//requete de base sur la table tri�e
				$requete = "SELECT " . $nomColonneIndex . " FROM " . $tableEnCours;
				if ($orderby!="") $requete .= " ORDER BY " . $orderby;
			}
    	}
 		return $requete;
	}
	
	public function appliquer_tri_from_tmp_table($idTri_orTri=0, $tableEnCours, $nomColonneIndex,$debLimit=0,$nbLimit=0){
		//r�cuperations des champs
		$fields = $this->params["FIELD"];
		
		//r�cup�ration de la description du tri
		if (is_array($idTri_orTri)) {
			$result = $idTri_orTri;
		}
		else {
			$result = $this->dSort->recupTriParId($idTri_orTri);
		}
		$trier_par = explode(",",$result['tri_par']);
		$trier_par = $this->ajoutTriForUniqueRender($trier_par);
		//parcours des champs sur lesquels trier
		$orderby = '';
		for ($j = 0; $j < count($trier_par); $j++) {
			//d�coupage du champ (ex : c_num_2 (croissance ou d�croissance (c ou d),
			//type de champ (num,text,...) et id du champ)
			$temp = explode("_", $trier_par[$j]);
			//on parcours tous les champs de tri possible
			for ($i = 0; $i < count($fields); $i++) {
				//afin de trouver ceux sur lesquels le tri s'applique
				if (isset($temp[2]) && $fields[$i]["ID"] == $temp[2]) {
					//on est sur un champ de tri
					//suivant le type de champ
					switch ($fields[$i]["TYPEFIELD"]) {
						case "internal":
							//c'est un champ de la requete de base
							$nomChamp = $fields[$i]["TABLEFIELD"][0]['value'];
							//on verifie que le champ est dans la table temporaire
							$requete_fields = pmb_mysql_query("SELECT * FROM " . $tableEnCours . " LIMIT 1");
							$x = 0;
							if ($requete_fields) {
								while ($x < pmb_mysql_num_fields($requete_fields)) {
									$ligne = pmb_mysql_fetch_field($requete_fields, $x);
									if ($ligne->name == $nomChamp) {
										//le champ est la donc on ajoute le champ au order
										if($orderby!="") $orderby.=",";
										$orderby .= $this->ajoutOrder($nomChamp,$temp[0]);
										$x = pmb_mysql_num_fields($requete_fields);
									}
									$x++;
								}
								pmb_mysql_free_result($requete_fields);
							}
							break;
						case "select":
							//une requete union est n�c�ssaire
							//le nom du champ on ajoute tb pour corriger le probleme des noms numeriques
							$nomChamp = "tb".$fields[$i]["NAME"];
							//on ajoute la colonne au orderby
							if($orderby!="") $orderby.=",";
							$orderby .= $this->ajoutOrder($nomChamp,$temp[0]);
							//on ajoute la colonne � la table temporaire
							$this->ajoutColonneTableTempo($tableEnCours, $nomChamp, $temp[1]);

							//on parcours la ou les tables pour generer les updates
							for ($x = 0; $x < count($fields[$i]["TABLE"]); $x++) {
								$requete = $this->genereRequeteUpdate($fields[$i]["TABLE"][$x], $tableEnCours, $nomChamp, $nomColonneIndex);
								pmb_mysql_query($requete);
							}
								
							//on a aussi des champs persos maitenant...
							if(isset($fields[$i]['SOURCE']) && $fields[$i]['SOURCE'] == "cp"){
								$requete = $this->generateRequeteCPUpdate($fields[$i], $tableEnCours, $nomChamp);
								pmb_mysql_query($requete);
							}
							break;
						case "authority":
						    //le nom du champ on ajoute tb pour corriger le probleme des noms numeriques
						    $nomChamp = "tb".$fields[$i]["NAME"];
						    //on ajoute la colonne au orderby
						    if($orderby!="") $orderby.=",";
						    //on ajoute la colonne au orderby
						    $orderby .= $this->ajoutOrder($nomChamp,$temp[0]);
						    
						    //on ajoute la colonne � la table temporaire
						    $this->ajoutColonneTableTempo($tableEnCours, $nomChamp, $temp[1]);
						    
						    //on a aussi des champs persos maitenant...
						    if(isset($fields[$i]['SOURCE']) && $fields[$i]['SOURCE'] == "cp"){
						        $requete = $this->generateRequeteCPAuthorityUpdate($fields[$i], $tableEnCours, $nomChamp);
						        pmb_mysql_query($requete);
						    }
						    break;
					} //switch
				} //if ($fields[$i]["ID"] == $temp[2]) {
			} //for ($i = 0; $i < count($fields); $i++) {
		} //for ($j = 0; $j < count($trier_par); $j++) {
	
		if ($orderby!="") {
			//on va classer la table tempo suivant les criteres donn�s
			$requete = "ALTER TABLE " . $tableEnCours ." ORDER BY ". $orderby;
			pmb_mysql_query($requete);
		}
	
		//on retourne la requete sur la table de tri
		if ($this->table_select!="") {
			//c'est une requete avec des informations ext�rieures
			$requete = "SELECT " . $nomColonneIndex . "," . $this->champs_select;
			$requete .= " FROM " . $this->table_tri_tempo . "," . $this->table_select;
			$requete .= " WHERE " . $this->table_select . "." . $this->table_primary_key_select;
			$requete .= "=" . $this->table_tri_tempo . "." . $nomColonneIndex;
			$requete .= " GROUP BY " . $nomColonneIndex;
			if ($orderby!="") $requete .= " ORDER BY " . $orderby;
			if ($nbLimit>0) $requete .= " LIMIT " . $debLimit . "," . $nbLimit;
		} else {
			if ($nbLimit>0) {
				//requete de base sur la table tri�e avec limit
				$requete = "SELECT * FROM " . $tableEnCours;
				if ($orderby!="") $requete .= " ORDER BY " . $orderby;
				$requete .= " LIMIT " . $debLimit . "," . $nbLimit;
			} else {
				//requete de base sur la table tri�e
				$requete = "SELECT " . $nomColonneIndex . " FROM " . $tableEnCours;
				if ($orderby!="") $requete .= " ORDER BY " . $orderby;
			}
		}
		return $requete;
	}
	
	/**
	 * Ajoute une colonne � la table temporaire du nom et du type pr�cis�
	 */
	public function ajoutColonneTableTempo($nomTable, $nomCol,$type) {
		
		//d'abord on ajoute la colonne
		$cmd_table = "ALTER TABLE " . $nomTable . " ADD " . $nomCol . " ";
		
		//en fonction du type on met le type mysql
		switch($type) {
			case "num":
				$cmd_table .= "integer";
				break;
			case "text":
			default:
				$cmd_table .= "text";
				break;
		}
		
		//execution de l'ajout de la colonne
		pmb_mysql_query($cmd_table);
	}
	
	/**
	 * Renvoi le nom du champ et l'ordre de tri SQL
	 */
	public function ajoutOrder($nomChp,$typeorder) {
		
		$tmpTxt = $nomChp;
		
		//suivant le type de tri
		switch ($typeorder) {
			case "c":
				$tmpTxt .= " ASC";
				break;
			case "d":
			default:
				$tmpTxt .= " DESC";
				break;
		}
		
		
		return $tmpTxt;
	}
	
	/**
	 * Genere les liaisons (jointures)
	 */
	protected static function genereRequeteLinks($desTable, $nomTable, $desLink, $params_reference, $params_referencekey) {
		$extractinfo_sql = "";
		if(isset($desLink["TABLE"][0]['ALIAS']) && $desLink["TABLE"][0]['ALIAS']){
			$alias = $desLink["TABLE"][0]['ALIAS'];
		}else{
			$alias =$desLink["TABLE"][0]['value'];
		}
		if(isset($desLink["TYPE"])) {
			switch ($desLink["TYPE"]) {
				case "n1" :
					if (isset($desLink["TABLEKEY"][0]['value']) && $desLink["TABLEKEY"][0]['value']) {
						$extractinfo_sql .= " LEFT JOIN " . $desLink["TABLE"][0]['value'].($desLink["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
						$extractinfo_sql .= " ON " . $desTable["NAME"] . "." . $desLink["EXTERNALFIELD"][0]['value'];
						$extractinfo_sql .= "=" . $alias . "." . $desLink["TABLEKEY"][0]['value'];
					} else {
						$extractinfo_sql .= " LEFT JOIN " . $desTable["NAME"];
						$extractinfo_sql .= " ON " . $params_reference . "." . $params_referencekey;
						$extractinfo_sql .= "=" . $desTable["NAME"] . "." . $desLink["EXTERNALFIELD"][0]['value'];
					}
					break;
				case "1n" :
					$extractinfo_sql .= " LEFT JOIN " . $desTable["NAME"];
					$extractinfo_sql .= " ON (" . $desTable["NAME"] . "." . $desTable["TABLEKEY"][0]['value'];
					$extractinfo_sql .= "=" . $params_reference . "." . $desLink["REFERENCEFIELD"][0]['value'] . ") ";
					break;
				case "nn" :
					$extractinfo_sql .= " LEFT JOIN " . $desLink["TABLE"][0]['value'].($desLink["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
					$extractinfo_sql .= " ON (" . $nomTable . "." . $params_referencekey;
					$extractinfo_sql .= "=" . $alias . "." . $desLink["REFERENCEFIELD"][0]['value'] . ") ";
						
					//Autres jointures
					if(isset($desLink["LINK"])) {
						for ($x = 0; $x <= count($desLink["LINK"]); $x++) {
							$extractinfo_sql .= static::genereRequeteLinks($desTable, $desLink["TABLE"][0]['value'], $desLink["LINK"][$x], $desLink["TABLE"][0]['value'], $desLink["EXTERNALFIELD"][0]['value']);
						}
					} else {
						if (isset($desLink["TABLEKEY"][0]['value']) && $desLink["TABLEKEY"][0]['value']) {
							$extractinfo_sql .= " LEFT JOIN " . $desTable["NAME"];
							$extractinfo_sql .= " ON (" . $alias . "." . $desLink["TABLEKEY"][0]['value'];
							$extractinfo_sql .= "=" . $desTable["NAME"] . "." . $desLink["EXTERNALFIELD"][0]['value'] ." ".$desLink["LINKRESTRICT"][0]['value']. ") ";
						} else {
							$extractinfo_sql .= " LEFT JOIN " . $desTable["NAME"];
							$extractinfo_sql .= " ON (" . $alias . "." . $desLink["EXTERNALFIELD"][0]['value'];
							$extractinfo_sql .= "=" . $desTable["NAME"] . "." . $desTable["TABLEKEY"][0]['value'] . " ".$desLink["LINKRESTRICT"][0]['value'].") ";
						
						}
					}
					break;
			}
		}
		return $extractinfo_sql;
	}
	
	/**
	 * Genere la requete select d'un element table
	 */
	public function genereRequeteUpdate($desTable, $nomTable, $nomChp, $nomColonneTempo) {
		global $lang;
		
		$tables = $nomTable . "," .$this->params["REFERENCE"];
		$groupby = "";
		
		//
		//SELECT de base pour la r�cup�ration des informations
		//
		$extractinfo_sql = "SELECT ".$this->params["REFERENCE"].'.'.$this->params["REFERENCEKEY"].", ".$this->ajoutIfNull($desTable["TABLEFIELD"][0])." AS ".$nomChp." FROM ".$nomTable.' LEFT JOIN '.$this->params["REFERENCE"].' ON ('.$this->params["REFERENCE"].'.'.$this->params["REFERENCEKEY"].' = '.$nomTable.'.'.$this->params["REFERENCEKEY"].')';
		
		//
		//On ajout les �ventuelles liaisons
		//
		if(isset($desTable["LINK"])) {
			for ($x = 0; $x <= count($desTable["LINK"]); $x++) {
				$extractinfo_sql .= static::genereRequeteLinks($desTable, $nomTable, $desTable["LINK"][$x], $this->params["REFERENCE"], $this->params["REFERENCEKEY"]);
			}
		}
		
		//si on a un filtre supplementaire
		if (isset($desTable["FILTER"])) {
			$extractinfo_sql .= " WHERE " . $desTable["FILTER"][0]['value'];	
		}
		
		//On applique la restriction ORDER BY
		//Utilis� pour les types de langues ou d'auteurs, ...
		if (isset($desTable["ORDERBY"])) {
			$extractinfo_sql .= " ORDER BY ".$this->ajoutIfNull($desTable["ORDERBY"][0]);		
		}
		
		//remplacement de motif pour les cat�gories
		$extractinfo_sql = str_replace('!!lang!!', $lang, $extractinfo_sql);
		
		//Si l'on a un group by on passe par une sous-requete pour que le groupement soit fait apr�s le tri (Cas des Auteurs : C'est l'auteur principal qui doit �tre utilis� pour le tri)
		if (isset($desTable["GROUPBY"])) {
			if (isset($desTable["ORDERBY"])) {
				// Si ORDER BY, on passe par une table temporaire car sinon il n'est pas pris en compte par le group by
				$sql = "DROP TEMPORARY TABLE IF EXISTS ".$nomTable."_groupby";
				pmb_mysql_query($sql);
				$temporary2_sql = "CREATE TEMPORARY TABLE ".$nomTable."_groupby ENGINE=MyISAM (".$extractinfo_sql.")";
					
				pmb_mysql_query($temporary2_sql);
				pmb_mysql_query("alter table ".$nomTable."_groupby add index(notice_id)");
					
				$extractinfo_sql = "SELECT * FROM ".$nomTable."_groupby";
				$extractinfo_sql .= " GROUP BY ".$desTable["GROUPBY"][0]["value"];
			} else {
				$extractinfo_sql = "SELECT * FROM (".$extractinfo_sql.") AS asubquery";
				$extractinfo_sql .= " GROUP BY ".$desTable["GROUPBY"][0]["value"];
			}
		}
		//
		//On met le tout dans une table temporaire
		//
		$sql = "DROP TEMPORARY TABLE IF EXISTS ".$nomTable."_update";
		pmb_mysql_query($sql);
		$temporary2_sql = "CREATE TEMPORARY TABLE ".$nomTable."_update ENGINE=MyISAM (".$extractinfo_sql.")";
		
		pmb_mysql_query($temporary2_sql);
		pmb_mysql_query("alter table ".$nomTable."_update add index(notice_id)");

		//
		//Et on rempli la table tri_tempo avec les �l�ments de la table temporaire
		//
		$requete = "UPDATE " . $this->params["REFERENCE"].", ".$nomTable.", ".$nomTable."_update";
		$requete .= " SET " . $nomTable.".".$nomChp . " = " . $nomTable."_update.".$nomChp;
		
		//le lien vers la table de tri temporaire
		$requete .= " WHERE " . $nomTable.".".$this->params["REFERENCEKEY"];
		$requete .= "=" . $nomTable."_update.".$this->params["REFERENCEKEY"];
		$requete .= " AND ".$this->params["REFERENCE"].".".$this->params["REFERENCEKEY"]."=".$nomTable.".".$this->params["REFERENCEKEY"];
		$requete .= " AND ".$nomTable."_update.".$nomChp." IS NOT NULL";
		$requete .= " AND ".$nomTable."_update.".$nomChp." != ''";
		
		return $requete;
	}


	/**
	 * Ajoute le ifnull si pr�cis�
	 */
	public function ajoutIfNull($tableau) {
		if (isset($tableau["NULLVALUE"])) {
			$tmpTxt = "IFNULL(" . $tableau['value'] . ",'" . $tableau["NULLVALUE"] . "')"; 
		} else {
			$tmpTxt = $tableau['value'];
		}
	
		return $tmpTxt;	
	}


	/**
	 * Parse les fichiers XML de parametres
	 * il y a un fichier par type de tris
	 */
	public function parse() {
		global $include_path;
		global $charset;
		global $dbh;
		$params_name = $this->dSort->sortName . "_params";
		global ${$params_name};
		$params = ${$params_name};

		if ($params) {
			$this->params = $params;
		} else {
			$nomfichier = $include_path . "/sort/" . $this->dSort->sortName . "/sort.xml";

			if (file_exists($include_path . "/sort/" . $this->dSort->sortName . "/sort_subst.xml")) {
				$nomfichier=$include_path . "/sort/" . $this->dSort->sortName . "/sort_subst.xml";
				$fp = fopen($nomfichier, "r");
			} else if (file_exists($nomfichier)) {
				$fp = fopen($nomfichier, "r");
			}

			if ($fp) {
				//un fichier est ouvert donc on le lit
				$xml = fread($fp, filesize($nomfichier));
				//on le ferme
				fclose($fp);
				//on le parse pour le transformer en tableau
				$params = _parser_text_no_function_($xml, "SORT", $nomfichier);
				//on le stocke dans la classe
				$this->params = $params;
			} else {
				$this->error = true;
				$this->error_message = "Can't open definition file";
			}
		}
		
		if (empty($this->params['PPERSOPREFIX'])) {
		    return;
		}
		
		//tri perso
		$p_perso = new parametres_perso("notices");
		
		foreach($p_perso->t_fields as $key => $t_field){
			$param=$t_field['OPTIONS'][0];
			switch($t_field['TYPE']){
				case "comment" :
				case "text":
					if(isset($param['REPETABLE']) && $param['REPETABLE'][0]['value']){
						$tablefield = "group_concat(".$p_perso->prefix."_custom_".$t_field['DATATYPE']." separator ' ')";
						$groupby = "group by notice_id";
					}else{
						$tablefield = $p_perso->prefix."_custom_".$t_field['DATATYPE'];
						$groupby = "";
					}
					$p_tri = array(
						'SOURCE' => "cp",
						'TYPEFIELD' => "select",
						'ID' => "cp".$key,
						'TYPE' => "text",
						'NAME' => $t_field['NAME'],
						'LABEL' => $t_field['TITRE'],
						'TABLEFIELD' => array('value'=>$tablefield),
						'REQ_SUITE' => "left join ".$p_perso->prefix."_custom_values on notices.notice_id = ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_origine where ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_champ = '".$key."' ".$groupby 
					);
					break;
				case "list":
					if($param['MULTIPLE'][0]['value']){
						$tablefield = "group_concat(".$p_perso->prefix."_custom_list_lib separator ' ')";
						$groupby = "group by notice_id";
					}else{
						$tablefield = $p_perso->prefix."_custom_list_lib";
						$groupby = "";
					}
					$p_tri = array(
						'SOURCE' => "cp",
						'TYPEFIELD' => "select",
						'ID' => "cp".$key,
						'TYPE' => "text",
						'NAME' => $t_field['NAME'],
						'LABEL' => $t_field['TITRE'],
						'TABLEFIELD' => array('value'=>$tablefield),
						'REQ_SUITE' => "left join ".$p_perso->prefix."_custom_values on notices.notice_id = ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_origine 
left join ".$p_perso->prefix."_custom_lists on ".$p_perso->prefix."_custom_".$t_field['DATATYPE']." = ".$p_perso->prefix."_custom_list_value 
where ".$p_perso->prefix."_custom_lists.".$p_perso->prefix."_custom_champ ='".$key."' and ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_champ ='".$key."'  ".$groupby 
					);
					break;
				case "date_box" :
					$p_tri = array(
						'SOURCE' => "cp",
						'TYPEFIELD' => "select",
						'ID' => "cp".$key,
						'TYPE' => "text",
						'NAME' => $t_field['NAME'],
						'LABEL' => $t_field['TITRE'],
						'TABLEFIELD' => array('value'=>$p_perso->prefix."_custom_".$t_field['DATATYPE']),
						'REQ_SUITE' => "left join ".$p_perso->prefix."_custom_values on notices.notice_id = ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_origine where ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_champ = '".$key."'"
					);
					break;
				case "query_list" :
					$tableid = "";
					$tablefield = "";
					$tablename = "";
					if($param['MULTIPLE'][0]['value']){
						if($param['QUERY'][0]['value']){
							$res = pmb_mysql_query($param['QUERY'][0]['value'],$dbh);
							if ($res) {
								$tableid = pmb_mysql_field_name($res,0);
								$tablefield = "group_concat(".pmb_mysql_field_name($res,1)." separator ' ')";
								$tablename = pmb_mysql_field_table($res,0);
							}
						}
						$groupby = "group by notice_id";
					} else {
						if($param['QUERY'][0]['value']){
							$res = pmb_mysql_query($param['QUERY'][0]['value'],$dbh);
							if ($res) {
								$tableid = pmb_mysql_field_name($res,0);
								$tablefield = pmb_mysql_field_name($res,1);
								$tablename = pmb_mysql_field_table($res,0);	
							}
						}
						$groupby = "";	
					}
			
					$p_tri = array(
						'SOURCE' => "cp",
						'TYPEFIELD' => "select",
						'ID' => "cp".$key,
						'TYPE' => "text",
						'NAME' => $t_field['NAME'],
						'LABEL' => $t_field['TITRE'],
						'TABLEFIELD' => array('value'=>$tablefield),
						'REQ_SUITE' => "left join ".$p_perso->prefix."_custom_values on notices.notice_id = ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_origine
left join ".$tablename." on ".$p_perso->prefix."_custom_".$t_field['DATATYPE']." = ".$tableid."						 
where ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_champ ='".$key."' ".$groupby 
					);
					break;
				case "query_auth" :
				    $p_tri = array(
				    	'SOURCE' => "cp",
				    	'TYPEFIELD' => "authority",
				    	'ID' => "cp".$key,
				    	'TYPE' => "text",
				    	'NAME' => $t_field['NAME'],
				    	'LABEL' => $t_field['TITRE'],
				    	'REQ_SUITE' => "left join ".$p_perso->prefix."_custom_values on notices.notice_id = ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_origine
where ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_champ ='".$key."' ",
						'PREFIX' => $p_perso->prefix,
						'T_FIELD' => $t_field
				    );
				    break;
				default : 
					$p_tri =array();
					break;
			}
			if($p_tri)$this->params['FIELD'][]=$p_tri;
		}
	}
	
	public function generateRequeteCPUpdate($field, $nomTable, $nomChp){
		$requete = "
			SELECT 
				".$this->params['REFERENCE'].'.'.$this->params['REFERENCEKEY'].", 
				".$this->ajoutIfNull($field['TABLEFIELD'])." AS ".$nomChp." 
			FROM ".$nomTable." LEFT JOIN ".$this->params['REFERENCE']." ON (".$this->params['REFERENCE'].".".$this->params['REFERENCEKEY']." = ".$nomTable.".".$this->params['REFERENCEKEY'].") 
				".$field['REQ_SUITE'];

		//On met le tout dans une table temporaire
		$sql = "DROP TEMPORARY TABLE IF EXISTS ".$nomTable."_update";
		pmb_mysql_query($sql);
		$temporary2_sql = "CREATE TEMPORARY TABLE ".$nomTable."_update ENGINE=MyISAM (".$requete.")";
		pmb_mysql_query($temporary2_sql);
		pmb_mysql_query("alter table ".$nomTable."_update add index(notice_id)");
	
		//
		//Et on rempli la table tri_tempo avec les �l�ments de la table temporaire
		//
		$requete = "UPDATE ".$nomTable.", ".$nomTable."_update";
		$requete .= " SET " . $nomTable.".".$nomChp . " = " . $nomTable."_update.".$nomChp;
		
		//le lien vers la table de tri temporaire
		$requete .= " WHERE " . $nomTable.".".$this->params["REFERENCEKEY"];
		$requete .= "=" . $nomTable."_update.".$this->params["REFERENCEKEY"];
		$requete .= " AND ".$nomTable."_update.".$nomChp." IS NOT NULL";
		$requete .= " AND ".$nomTable."_update.".$nomChp." != ''";

		return $requete;
	}
	
	public function generateRequeteCPAuthorityUpdate($field, $nomTable, $nomChp){
	    $requete = "
			SELECT
				".$this->params['REFERENCE'].'.'.$this->params['REFERENCEKEY']." AS tbCPId,
				".$field['PREFIX']."_custom_".$field['T_FIELD']['DATATYPE']." AS tbCPAuthority
			FROM ".$nomTable." LEFT JOIN ".$this->params['REFERENCE']." ON (".$this->params['REFERENCE'].".".$this->params['REFERENCEKEY']." = ".$nomTable.".".$this->params['REFERENCEKEY'].")
				".$field['REQ_SUITE'];
	    $result = pmb_mysql_query($requete);
	    $objects_ids = array();
	    if(pmb_mysql_num_rows($result)) {
	        while ($row = pmb_mysql_fetch_object($result)) {
	            $objects_ids[$row->tbCPId] = get_authority_isbd_from_field($field['T_FIELD'], $row->tbCPAuthority);
	        }
	    }
	    
	    //On met le tout dans une table temporaire
	    $sql = "DROP TEMPORARY TABLE IF EXISTS ".$nomTable."_update";
	    pmb_mysql_query($sql);
	    $temporary2_sql = "CREATE TEMPORARY TABLE ".$nomTable."_update (
            ".$this->params['REFERENCEKEY']." INTEGER,
            ".$nomChp." TEXT
        ) ENGINE=MyISAM";
	    pmb_mysql_query($temporary2_sql);
	    pmb_mysql_query("alter table ".$nomTable."_update add index(".$this->params['REFERENCEKEY'].")");
	    
	    foreach ($objects_ids as $object_id=>$authority_value) {
	        $query = "INSERT INTO ".$nomTable."_update
                SET ".$this->params['REFERENCEKEY']." = ".$object_id.",
                ".$nomChp . " = '" .addslashes($authority_value)."'";
	        pmb_mysql_query($query);
	    }
	    
	    //
	    //Et on rempli la table tri_tempo avec les �l�ments de la table temporaire
	    //
	    $requete = "UPDATE ".$nomTable.", ".$nomTable."_update";
	    $requete .= " SET " . $nomTable.".".$nomChp . " = " . $nomTable."_update.".$nomChp;
	    
	    //le lien vers la table de tri temporaire
	    $requete .= " WHERE " . $nomTable.".".$this->params["REFERENCEKEY"];
	    $requete .= "=" . $nomTable."_update.".$this->params["REFERENCEKEY"];
	    $requete .= " AND ".$nomTable."_update.".$nomChp." IS NOT NULL";
	    $requete .= " AND ".$nomTable."_update.".$nomChp." != ''";
	    
	    return $requete;
	}
}
?>