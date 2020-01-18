<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: emprunteur.class.php,v 1.33 2019-06-18 07:52:20 ngantier Exp $

// classe emprunteur
//	inclure :
//	./classes/notice_display.class.php

if ( ! defined( 'EMPR_CLASS' ) ) {
  define( 'EMPR_CLASS', 1 );

class emprunteur {

	//---------------------------------------------------------
	//			Propri�t�s
	//---------------------------------------------------------
	public $id		= 0		;	// id MySQL emprunteur
	public $cb		= ''	;	// code barre emprunteur
	public $nom	= ''	;	// nom emprunteur
	public $prenom	= ''	;	// pr�nom emprunteur
	public $adr1	= ''	;	// adresse ligne 1
	public $adr2	= ''	;	// adresse ligne 2
	public $cp		= ''	;	// code postal
	public $ville	= ''	;	// ville
	public $mail	= ''	;	// adresse email
	public $tel1	= ''	;	// t�l�phone 1
	public $tel2	= ''	;	// t�l�phone 2
	public $prof	= ''	;	// profession
	public $birth	= ''	;	// ann�e de naissance
	public $categ 	= 0		;	// cat�gorie emprunteur
	public $cat_l	= ''	;	// libell� cat�gorie emprunteur
	public $cstat	= 0		;	// code statistique
	public $cstat_l= 0		;	// libell� code statistique
	public $cdate	= ''	;	// date de cr�ation
	public $mdate	= ''	;	// date de modification
	public $adate	= ''	;	// date d'abonnement
	public $rdate	= ''	;	// date de r�abonnement
	public $sexe	= 0		;	// sexe de l'emprunteur
	public $login	= ''	;	// login pour services OPAC
	public $pwd 	= ''	;	// mot de passe OPAC
	public $date_adhesion   = ''	;	// d�but adh�sion
	public $date_expiration = ''	;	// fin adh�sion
	public $aff_date_adhesion   = '';	// d�but adh�sion format�e
	public $aff_date_expiration = '';	// fin adh�sion format�e
	public $prets			;	// array contenant les pr�ts de l'emprunteur
	public $reservations	;	// array contenant les r�servations pour l'emprunteur
	public $message = ''	;	// cha�ne contenant les messages emprunteurs
	public $fiche = ''		;	// code HTML de la fiche lecteur
	public $serious_message=FALSE;	// niveau du message (s�rieux si TRUE)
	public $devices;			// liste des liseuses compatibles avec le PNB
	protected $pnb_password;	// mot de passe utilis� dans le PNB (pour l'ouverture des epubs)
	protected $pnb_password_hint; //Indice du mot de passe du pnb
	// <----------------- constructeur ------------------>
	public function __construct($id=0, $message='', $niveau_message=FALSE) {
	
		// initialisation des propri�t�s si l'id est d�fini
		$id +=0;
		if($id) {
			$this->id = $id;
			$this->serious_message = $niveau_message;
			$this->prets = array();
			$this->reservations = array();
			$this->fetch_info();
			$this->message = $message;
			$this->do_fiche();
		}
	}

	//   renseignement des propri�t�s avec requ�te MySQL
	public function fetch_info() {
		global $msg ;
		global $dbh;
	
		if(!$this->id || !$dbh)
			return FALSE;
	
		$requete = "SELECT e.*, c.libelle AS code1, s.libelle AS code2, date_format(empr_date_adhesion, '".$msg["format_date_sql"]."') as aff_empr_date_adhesion, date_format(empr_date_expiration, '".$msg["format_date_sql"]."') as aff_empr_date_expiration  FROM empr e, empr_categ c, empr_codestat s";
		$requete .= " WHERE e.id_empr='".$this->id."' " ;
		$requete .= " AND c.id_categ_empr=e.empr_categ";
		$requete .= " AND s.idcode=e.empr_codestat";
		$requete .= " LIMIT 1";
		$result = pmb_mysql_query($requete, $dbh);
	
		$empr = pmb_mysql_fetch_object($result);
	
		// affectation des propri�t�s
		$this->cb		= $empr->empr_cb			;	// code barre emprunteur
		$this->nom		= $empr->empr_nom			;	// nom emprunteur
		$this->prenom	= $empr->empr_prenom		;	// pr�nom emprunteur
		$this->adr1		= $empr->empr_adr1			;	// adresse ligne 1
		$this->adr2		= $empr->empr_adr2			;	// adresse ligne 2
		$this->cp		= $empr->empr_cp			;	// code postal
		$this->ville	= $empr->empr_ville			;	// ville
		$this->mail		= $empr->empr_mail			;	// adresse email
		$this->tel1		= $empr->empr_tel1			;	// t�l�phone 1
	 	$this->tel2		= $empr->empr_tel2			;	// t�l�phone 2
		$this->prof		= $empr->empr_prof			;	// profession
		$this->birth	= $empr->empr_year			;	// ann�e de naissance
		$this->categ 	= $empr->empr_categ			;	// cat�gorie emprunteur
		$this->cstat	= $empr->empr_codestat		;	// code statistique
		$this->cdate	= $empr->empr_creation		;	// date de cr�ation
		$this->mdate	= $empr->empr_modif			;	// date de modification
		$this->sexe		= $empr->empr_sexe			;	// sexe de l'emprunteur
		$this->login	= $empr->empr_login			;	// login pour services OPAC
		$this->pwd 		= $empr->empr_password		;	// mot de passe OPAC
		$this->date_adhesion 	= $empr->empr_date_adhesion		;	// d�but adh�sion
		$this->date_expiration 	= $empr->empr_date_expiration		;	// fin adh�sion
		$this->aff_date_adhesion 	= $empr->aff_empr_date_adhesion		;	// d�but adh�sion
		$this->aff_date_expiration 	= $empr->aff_empr_date_expiration		;	// fin adh�sion
		$this->cat_l	= $empr->code1				;	// libell� cat�gorie emprunteur
		$this->cstat_l	= $empr->code2				;	// libell� code statistique. voir ce bug avec Eric
	
		// ces propri�t�s sont absentes de la table emprunteurs pour le moment
		//	$this->message	= $empr->empr_???	;	// cha�ne contenant les messages emprunteurs
		//	$this->adate	= $empr->empr_???	;	// date d'abonnement
		//	$this->rdate	= $empr->empr_???	;	// date de r�abonnement
		if($this->message){
			$this->message	= $empr->empr_msg.'<hr />'.$this->message;
		}else{
			$this->message = $empr->empr_msg;
		}
		// r�cup�ration du tableau des exemplaires emprunt�s
		// il nous faut : code barre exemplaire, titre/auteur, type doc, date de pr�t, date de retour
		$requete = "select e.expl_cb, e.expl_notice, p.pret_date, p.pret_retour, t.tdoc_libelle, date_format(pret_date, '".$msg["format_date_sql"]."') as aff_pret_date, date_format(pret_retour, '".$msg["format_date_sql"]."') as aff_pret_retour, if (pret_retour< CURDATE(),1 ,0 ) as retard ";
		$requete .= " from pret p, exemplaires e, docs_type t";
		$requete .= " where p.pret_idempr=".$this->id;
		$requete .= " and p.pret_idexpl=e.expl_id";
		$requete .= " and t.idtyp_doc=e.expl_typdoc";
		$requete .= " order by p.pret_date";
	
		$result = pmb_mysql_query($requete, $dbh);
		while($pret = pmb_mysql_fetch_object($result)) {
			$notice = new notice_affichage($pret->expl_notice,0,0,0);
			$notice->do_header();
			$this->prets[] = array(
						'cb' => $pret->expl_cb,
						'libelle' => $notice->notice_header,
						'typdoc' => $pret->tdoc_libelle,
						'date_pret' => $pret->aff_pret_date,
						'date_retour' => $pret->aff_pret_retour,
						'org_ret_date' => str_replace('-', '', $pret->pret_retour)
						);
	
		}
	
		return TRUE;
	
	}

	// fabrication de la fiche lecteur
	public function do_fiche() {
		global $empr_tmpl;
		global $msg;
	
		$this->fiche = $empr_tmpl;
		$this->fiche = str_replace('!!cb!!'		, $this->cb		, $this->fiche);
		$this->fiche = str_replace('!!nom!!'	, pmb_strtoupper($this->nom)	, $this->fiche);
		$this->fiche = str_replace('!!prenom!!'	, $this->prenom	, $this->fiche);
		$this->fiche = str_replace('!!id!!'		, $this->id		, $this->fiche);
		$this->fiche = str_replace('!!adr1!!'	, $this->adr1	, $this->fiche);
		$this->fiche = str_replace('!!adr2!!'	, $this->adr2	, $this->fiche);
		$this->fiche = str_replace('!!tel1!!'	, $this->tel1	, $this->fiche);
		$this->fiche = str_replace('!!tel2!!'	, $this->tel2	, $this->fiche);
		$this->fiche = str_replace('!!cp!!'		, $this->cp		, $this->fiche);
		$this->fiche = str_replace('!!ville!!'	, $this->ville	, $this->fiche);
		$emails=array();
		$email_final=array();
		$emails = explode(';',$this->mail);
		for ($i=0;$i<count($emails);$i++) $email_final[] ="<a href='mailto:".$emails[$i]."'>".$emails[$i]."</a>";
		
		$this->fiche = str_replace('!!mail_all!!'	, $this->mail	, $this->fiche);
		$this->fiche = str_replace('!!prof!!'	, $this->prof	, $this->fiche);
		$this->fiche = str_replace('!!date!!'	, $this->birth	, $this->fiche);
		$this->fiche = str_replace('!!categ!!'	, $this->categ.'-'.$this->cat_l	, $this->fiche);
		$this->fiche = str_replace('!!codestat!!'	, $this->cstat.'-'.$this->cstat_l	, $this->fiche);
		$this->fiche = str_replace('!!adhesion!!'	, $this->aff_date_adhesion, $this->fiche);
		$this->fiche = str_replace('!!expiration!!'	, $this->aff_date_expiration, $this->fiche);
	
		if($this->serious_message) $this->fiche = str_replace('!!class_msg!!'	, 'empr-serious-msg', $this->fiche);
			else $this->fiche = str_replace('!!class_msg!!'	, 'empr-msg', $this->fiche);
		if(!$this->message) $this->message = $msg["empr_no_message_for"];
		
		$this->fiche = str_replace('!!empr_msg!!'	, $this->message	, $this->fiche);
	
		$fsexe[0] = $msg[128];
		$fsexe[1] = $msg[126];
		$fsexe[2] = $msg[127];
	
		$this->fiche = str_replace('!!sexe!!'	, $fsexe[$this->sexe], $this->fiche);
	
		// valeur pour les champ hidden du pr�t. L'id empr est pris en charge plus haut (voir Eric)
		$this->fiche = str_replace('!!cb!!'	, $this->cb	, $this->fiche);
	
		// traitement liste exemplaires en pr�t
		if(!sizeof($this->prets))
			// dans ce cas, le lecteur n'a rien en pr�t
			$prets_list = "<tr><td class='ex-strip' colspan='5'>".$msg["empr_no_expl"]."</td></tr>";
			// voir la localisation retenue par Eric
		else {
			// constitution du code HTML
			$prets_list = "";
			foreach ($this->prets as $cle => $valeur) {
				$prets_list .= "
				<tr>
				<form name=prolong${valeur['cb']} action='circ.php'>
					<td class='strip'>
						${valeur['cb']}
					</td>
					<td class='empr-msg'>
						${valeur['libelle']}
					</td>
					<td class='strip'>
						${valeur['typdoc']}
					</td>
					<td class='strip'>
						${valeur['date_pret']}
					</td>
					<td class='strip'>
						<input type='hidden' name='categ' value='pret'>\n
						<input type='hidden' name='sub' value='pret_prolongation'>\n
						<input type='hidden' name='form_cb' value='$this->cb'>\n
						<input type='hidden' name='cb_doc' value='${valeur['cb']}'>\n
						<input type='hidden' name='date_retour' value=\"\">\n
					";
					$prets_list .="	</td>
							</form></tr>
							";
					// ouf, c'est fini ;-)
			}
		}
		$this->fiche = str_replace('!!pret_list!!'	, $prets_list	, $this->fiche);
		// mise � jour de la liste des r�servations
	
		$this->fiche = str_replace('!!resa_list!!', $this->fetch_resa(), $this->fiche);
	}

	// r�cup�ration de la liste des r�servations pour l'emprunteur
	public function fetch_resa() {
		global $dbh;
		global $msg ;
	
		$affiche = '';
		// on commence par v�rifier si l'emprunteur a des r�servations
		$query = "select count(1) from resa where resa_idempr=".$this->id;
		$result = pmb_mysql_query($query, $dbh);
		if(!@pmb_mysql_result($result, 0, 0))
			return $msg["empr_no_resa"];
	
		// si le lecteur a r�serv� un ou des documents, on r�cup�re tout
		$query = "select * from resa ";
		$query .= " where resa.resa_idempr=".$this->id;
	
		$result = pmb_mysql_query($query, $dbh);
	
		while($resa = pmb_mysql_fetch_object($result)) {
			// constitution du tableau des r�servations
			// on r�cup�re le rang du r�servataire
			$rang = $this->get_rank($this->id, $resa->resa_idnotice);
	
			// maintenant, on s'accroche : d�termination de la date � afficher dans la case retour :
			// disponible, r�serv� ou date de retour du premier exemplaire
	
			// on compte le nombre total d'exemplaires pour la notice
			$query = "select count(1) from exemplaires, docs_statut where expl_notice=".$resa->resa_idnotice;
			$query .= " and expl_statut=idstatut and pret_flag=1";
			$tresult = @pmb_mysql_query($query, $dbh);
			$total_ex = @pmb_mysql_result($tresult, 0, 0);
	
			// on compte le nombre total de r�servations sur la notice
			$query = "select count(1) from resa where resa_idnotice=".$resa->resa_idnotice;
			$tresult = @pmb_mysql_query($query, $dbh);
			$total_resa = @pmb_mysql_result($tresult, 0, 0);
	
			// on compte le nombre d'exemplaires sortis
			$query = "select count(1) from exemplaires e, pret p";
			$query .= " where e.expl_notice=".$resa->resa_idnotice;
			$query .= " and p.pret_idexpl=e.expl_id";
			$tresult = @pmb_mysql_query($query, $dbh);
			$total_sortis = @pmb_mysql_result($tresult, 0, 0);
	
			// on en d�duit le nombre d'exemplaires disponibles
			$total_dispo = $total_ex - $total_sortis;
	
			if($rang <= $total_dispo) {
				// un exemplaire est disponible pour le r�servataire (affichage : disponible)
				$situation = "<span style='color:#ff0000'><strong>".$msg["available"]."</strong></span>";
			} else {
				if($total_dispo) {
					// un ou des exemplaires sont disponibles, mais pas pour ce r�servataire (affichage : reserv�)
					$situation = $msg["expl_reserve"];
				} else {
					// rien n'est disponible, on trouve la date du premier retour
					$query = 'select (pret_retour) from pret p, exemplaires e';
					$query .= ' where e.expl_notice='.$resa->resa_idnotice;
					$query .= ' and e.expl_id=p.pret_idexpl';
					$query .= ' order by p.pret_retour limit 1';
					$tresult = pmb_mysql_query($query, $dbh);
					$first_ret = pmb_mysql_fetch_object($tresult);
					$situation = formatdate($first_ret->pret_retour);
				}
			}
	
			$notice = new notice_affichage($resa->resa_idnotice,0,0,0);
			$notice->do_header();
			$affiche .= "<tr><td class='strip'>".$notice->notice_header;
			$affiche .= "<td class='strip'>$rang/$total_resa</td>";
			$affiche .= "<td class='strip'>$situation</td>";
			$del_link = "<a href='./circ.php?categ=resa&id_empr=".$this->id.'&id_notice='.$resa->resa_idnotice."&delete=1'>";
			$affiche .= "<td class='center'>$del_link<img style='border:0px' src='".get_url_icon('trash.gif')."'></a></td></tr>";
		}
		return $affiche;
	
	}

	// <----------------- get_rank() ------------------>
	//   calcul du rang d'un emprunteur sur une r�servation
	
	public function get_rank($id_empr, $id_notice) {
		global $dbh;
		$rank = 1;
		$query = "select * from resa where resa_idnotice=".$id_notice." order by resa_date";
		$result = pmb_mysql_query($query, $dbh);
		while($resa=pmb_mysql_fetch_object($result)) {
			if($resa->resa_idempr == $id_empr)
				break;
			$rank++;
		}
		return $rank;
	}

	public static function get_hashed_password($empr_login='',$empr_password='') {
		global $dbh;
	
		$id_empr = 0;
		if ($empr_login) {
			$query = "select id_empr from empr where empr_login='".$empr_login."'";
			$result = pmb_mysql_query($query,$dbh);
			if (pmb_mysql_num_rows($result) == 1) {
				$id_empr = pmb_mysql_result($result, 0, "id_empr");
			}
		}
		if ($id_empr) {
			return password::gen_hash($empr_password, $id_empr);
		} else {
			return "";
		}
	}

	public static function hash_password($empr_login='',$empr_password='') {
		global $dbh;
		global $opac_empr_password_salt;
		if (!$opac_empr_password_salt) {
			$salt_base = password::gen_salt_base();
			if (!$salt_base) return false;
		}
	
		$id_empr = 0;
		if ($empr_login) {
			$query = "select id_empr from empr where empr_login='".addslashes($empr_login)."'";
			$result = pmb_mysql_query($query,$dbh);
			if (pmb_mysql_num_rows($result) == 1) {
				$id_empr = pmb_mysql_result($result, 0, "id_empr");
			}
		}
		if ($id_empr) {
			$q = "update empr set empr_password='".addslashes(password::gen_hash($empr_password, $id_empr))."', empr_password_is_encrypted = 1 where empr_login='".addslashes($empr_login)."'";
			pmb_mysql_query($q,$dbh);
		}
	}

	public static function update_digest($empr_login='',$empr_password='') {
		global $dbh, $pmb_url_base;
	
		if (!$empr_login) return;
	
		$q = "update empr set empr_digest='".addslashes(md5($empr_login.":".md5($pmb_url_base).":".$empr_password))."' where empr_login='".addslashes($empr_login)."'";
		pmb_mysql_query($q,$dbh);
	
	}
	
	public function get_devices(){
		if(!isset($this->devices)){
			$this->devices = array();
			$query = 'select device_id from empr_devices where empr_num = '.$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_assoc($result)){
					$this->devices[] = $row['device_id'];
				}
			}
		}
		return $this->devices;
	}
	
	public function set_devices($devices_id){
		$this->devices = $devices_id;
	}
	
	public function save_devices(){
		$query = "delete from empr_devices where empr_num = ".$this->id;
		pmb_mysql_query($query);
		if(isset($this->devices) && is_array($this->devices) && count($this->devices)){
			$query = 'insert into empr_devices (empr_num, device_id) values ';
			$sub_query = '';
			foreach($this->devices as $device_id){
				if($sub_query){
					$sub_query.= ',';
				}
				$sub_query.= '('.$this->id.', '.$device_id.') ';
			}
			$query.= $sub_query;
			pmb_mysql_query($query);
		}
	}

	public function get_pnb_password(){
		return $this->pnb_password;
	}
	
	public function get_pnb_password_hint(){
		return $this->pnb_password_hint;
	}
	
	public function set_pnb_password($pnb_password){
		$this->pnb_password = $pnb_password;
	}
	
	public function set_pnb_password_hint($pnb_password_hint){
		$this->pnb_password_hint = $pnb_password_hint;
	}
	
	public function save_pnb_password(){
		/**
		 * TODO : fonction d'initialisation du mot de passe au cas ou 
		 * d'autres param�tres sont ajout�s (afin de ne pas avoir a r�entrer le mot de passe � chaque fois
		 */
		if(!empty($this->pnb_password)){
			$password = base64_encode(hash('sha256', $this->pnb_password));
			$query = "update empr set empr_pnb_password = '".$password."' where id_empr = ".$this->id;
			pmb_mysql_query($query);
		}
	}
	
	public function save_pnb_password_hint(){
		$query = "update empr set empr_pnb_password_hint = '".$this->pnb_password_hint."' where id_empr = ".$this->id;
		pmb_mysql_query($query);
	}
	
	public function init_pnb_parameters(){
		$query = 'select empr_pnb_password, empr_pnb_password_hint from empr where id_empr = '.$this->id;
		$result = pmb_mysql_query($query);
		
		if(pmb_mysql_num_rows($result)){
			$parameters = pmb_mysql_fetch_assoc($result);
			$this->pnb_password = $parameters['empr_pnb_password'];
			$this->pnb_password_hint = $parameters['empr_pnb_password_hint'];
		}
	}
	
	public function update_empr_status($status) {
	    $status = intval($status);	    
	    if ($status && $this->id) {
	        $query = "
                UPDATE empr SET empr_statut = $status WHERE id_empr = $this->id
            ";
	        pmb_mysql_query($query);
	    }
	}
	
	/**
	 * Renvoi du mail de confirmation d'inscription
	 */
	public function registration_confirmation_email() {
		global $msg, $charset;
		global $opac_biblio_name,$opac_biblio_email,$opac_url_base ;
		global $opac_url_base;
		
		$obj = str_replace("!!biblio_name!!",$opac_biblio_name,$msg['subs_mail_obj']) ;
		$corps = str_replace("!!biblio_name!!",$opac_biblio_name,$msg['subs_mail_corps']) ;
		$corps = str_replace("!!empr_first_name!!", $this->prenom,$corps) ;
		$corps = str_replace("!!empr_last_name!!",$this->nom,$corps) ;
		
		// nouvelle cl� de validation :
		$alphanum  = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
		$cle_validation = substr(str_shuffle($alphanum), 0, 20);
		$query = "UPDATE empr set cle_validation = '".$cle_validation."' WHERE id_empr = ".$this->id;
		pmb_mysql_query($query);
		
		$lien_validation = "<a href='".$opac_url_base."subscribe.php?subsact=validation&login=".urlencode($this->login)."&cle_validation=$cle_validation'>".$opac_url_base."subscribe.php?subsact=validation&login=".$this->login."&cle_validation=$cle_validation</a>";
		$corps = str_replace("!!lien_validation!!",$lien_validation,$corps) ;
		
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\n";
		
		return mailpmb(trim($this->prenom." ".$this->nom), $this->mail, $obj, $corps, $opac_biblio_name, $opac_biblio_email, $headers);
	}
	
} # fin de d�claration classe emprunteur

} # fin de d�finition
?>
