<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: author.inc.php,v 1.46 2015-04-03 11:16:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// premier niveau de recherche OPAC sur auteurs

// inclusion classe pour affichage auteur (level 1)
require_once($base_path.'/includes/templates/author.tpl.php');
require_once($base_path.'/classes/author.class.php');

if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);

// on regarde comment la saisie utilisateur se présente
$clause = '';
$add_notice = '';

$aq=new analyse_query(stripslashes($user_query),0,0,1,1);
$members=$aq->get_query_members("authors","concat(author_name,', ',author_rejete)","index_author","author_id");
$clause =' where '.$members["where"];

if ($opac_search_other_function) $add_notice=search_other_function_clause();

if ($typdoc || $add_notice) $clause = ',notices, responsability '.$clause.' and responsability_author=author_id and notice_id=responsability_notice';

if ($typdoc) $clause.=" and typdoc='".$typdoc."' ";

if ($add_notice) $clause.= ' and notice_id in ('.$add_notice.')'; 

$tri = 'order by pert desc, index_author';
$pert=$members["select"]." as pert";


$auteurs = pmb_mysql_query("SELECT COUNT(distinct author_id) FROM authors $clause and author_type='70' ", $dbh);
$nb_result_auteurs_physiques = pmb_mysql_result($auteurs, 0 , 0);
$auteurs = pmb_mysql_query("SELECT COUNT(distinct author_id) FROM authors $clause and author_type='71' ", $dbh);
$nb_result_auteurs_collectivites = pmb_mysql_result($auteurs, 0 , 0);
$auteurs = pmb_mysql_query("SELECT COUNT(distinct author_id) FROM authors $clause and author_type='72' ", $dbh);
$nb_result_auteurs_congres = pmb_mysql_result($auteurs, 0 , 0);
$nb_result_auteurs=$nb_result_auteurs_physiques+$nb_result_auteurs_collectivites+$nb_result_auteurs_congres;

//Enregistrement des stats
if($pmb_logs_activate){
	global $nb_results_tab;
	$nb_results_tab['auteurs'] = $nb_result_auteurs;
	$nb_results_tab['collectivites'] = $nb_result_auteurs_collectivites;
	$nb_results_tab['congres'] = $nb_result_auteurs_congres;
	$nb_results_tab['physiques'] = $nb_result_auteurs_physiques;
}

if($nb_result_auteurs_physiques == $nb_result_auteurs) {
	// Il n'y a que des auteurs physiques, affichage type: Auteurs xx résultat(s) afficher
	$titre_resume[0]=$msg["authors"];
	$nb_result_resume[0]=$nb_result_auteurs;
	$link_type_resume[0]="70";
} else if($nb_result_auteurs_collectivites == $nb_result_auteurs) {
	// Il n'y a que des collectivites, affichage type: Collectivités xx résultat(s) afficher
	$titre_resume[0]=$msg["collectivites_search"];
	$nb_result_resume[0]=$nb_result_auteurs;
	$link_type_resume[0]="71";
} else if($nb_result_auteurs_congres == $nb_result_auteurs) {
	// Il n'y a que des congres, affichage type: Collectivités xx résultat(s) afficher
	$titre_resume[0]=$msg["congres_search"];
	$nb_result_resume[0]=$nb_result_auteurs;
	$link_type_resume[0]="72";
} else {
	// il y a un peu de tout, affichage en titre type: Auteurs xx résultat(s) afficher
	$titre_resume[0]=$msg["authors"];
	$nb_result_resume[0]=$nb_result_auteurs;
	$link_type_resume[0]="";

	if($nb_result_auteurs_physiques) {
	// Il n'y a des auteurs physiques, affichage en sous-titre titre: Auteurs physiques xx résultat(s) afficher
		$titre_resume[]=$msg["personnes_physiques_search"];
		$nb_result_resume[]=$nb_result_auteurs_physiques;
		$link_type_resume[]="70";
	}
	if($nb_result_auteurs_collectivites) {
		// Il n'y a des collectivites, affichage en sous-titre titre: Collectivités xx résultat(s) afficher
		$titre_resume[]=$msg["collectivites_search"];
		$nb_result_resume[]=$nb_result_auteurs_collectivites;
		$link_type_resume[]="71";
	}
	if($nb_result_auteurs_congres) {
		// Il n'y a des congres, affichage en sous-titre titre: Congrès xx résultat(s) afficher
		$titre_resume[]=$msg["congres_search"];
		$nb_result_resume[]=$nb_result_auteurs_congres;
		$link_type_resume[]="72";
	}
}

if($opac_allow_affiliate_search){
	print "
	<div id='author_result'>
		<strong>".$titre_resume[0]."</strong>";

	print"
		<blockquote id='author_result_blockquote'>
			<div id='author_results_in_catalog'>";
	for($i=0;$i<count($titre_resume);$i++)  {
		if($i==0){
			print "
				<strong>".$msg['in_catalog']."</strong> ".$nb_result_resume[$i]." ".$msg['results'];
		}else{
			if($i==1) print "<blockquote>";
			print "
				<strong>".$titre_resume[$i]."</strong> ".$nb_result_resume[$i]." ".$msg['results'];
		}
			// Le lien validant le formulaire est inséré avant le formulaire, cela évite les blancs à l'écran
			if($link_type_resume[$i]) {
				$clause_link=$clause." and author_type='".$link_type_resume[$i]."'";
			} else {
				$clause_link=$clause;
			}
			if ($nb_result_resume[$i]) {
				print "<a href=\"#\" onClick=\"
				document.forms.search_authors.count.value='".$nb_result_resume[$i]."';
				document.forms.search_authors.clause.value='".htmlentities(addslashes($clause_link),ENT_QUOTES,$charset)."';
				document.forms.search_authors.author_type.value='$link_type_resume[$i]';
				document.forms.search_authors.action ='./index.php?lvl=more_results&tab=catalog';
				document.forms['search_authors'].submit(); return false;\">".$msg['suite']."&nbsp;<img src=./images/search.gif border='0' align='absmiddle'/></a>";
			}
			print "<br />";
	}
	if($i>1) print "</blockquote>";
	print "
		</div>
		<div id='author_results_affiliate'>
			<strong>".$msg['in_affiliate_source']."</strong><img src='images/patience.gif' />
		</div>
		<script type='text/javascript'>
			var author_search = new http_request();
			author_search.request('./ajax.php?module=ajax&categ=search',true,'&search_type=authorities&type=author&user_query=".rawurlencode(stripslashes((($charset == "utf-8")?$user_query:utf8_encode($user_query))))."',true,authorResults);
			function authorResults(response){
				var rep = eval('('+response+')');
				var div = document.getElementById('author_results_affiliate');
				div.innerHTML='';
				var strong = document.createElement('strong');
				strong.innerHTML = \"".$msg['in_affiliate_source']."\";
				div.appendChild(strong);
				var text_node = document.createTextNode(' '+ rep.nb_results.total + ' ". $msg['results']." ');
				div.appendChild(text_node);
				if(rep.nb_results.total>0){
					var a = document.createElement('a');
					a.setAttribute('href','#');
					a.innerHTML = \"".$msg['suite']."&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/>\";
					if(a.addEventListener){
						a.addEventListener('click',function(){
							document.search_authors.action='./index.php?lvl=more_results&tab=affiliate';
							document.search_authors.submit();
							return false;
						},true);
					}else if(a.attachEvent){
						a.attachEvent('onclick',function(){
							document.search_objects.action='./index.php?lvl=more_results&tab=affiliate';
							document.search_objects.submit();
							return false;
						});
					}else{
						a.addEvent('onclick',function(){
							document.search_authors.action='./index.php?lvl=more_results&tab=affiliate';
							document.search_authors.submit();
							return false;
						});
					}
					div.appendChild(a);
					var test = (rep.nb_results.authors>0 && (rep.nb_results.coll>0 || rep.nb_results.congres>0))|| (rep.nb_results.coll>0 && (rep.nb_results.authors>0 || rep.nb_results.congres>0));
					if(test){
						var bool = false;
						var block = document.createElement('blockquote');
						if(rep.nb_results.authors>0){
							createItem(rep.nb_results.authors,'".$msg['personnes_physiques_search']."','70',block);
							bool = true;
						}
						if(rep.nb_results.coll>0){
							if(bool) block.appendChild(document.createElement('br'));
							createItem(rep.nb_results.coll,'".$msg['collectivites_search']."','71',block);
							bool = true;
						}
						if(rep.nb_results.congres>0){
							if(bool) block.appendChild(document.createElement('br'));
							createItem(rep.nb_results.congres,'".$msg['congres_search']."','72',block);
						}
						div.appendChild(block);
					}
					document.getElementById('author_result').style.display = 'block';
				}
			}

			function createItem(nb_results,label,type,container){
				var span = document.createElement('span');
				span.innerHTML = '<strong>'+label+'</strong> '+ nb_results + ' ". $msg['results']." ';
				var a = document.createElement('a');
				a.setAttribute('href','#');
				a.innerHTML = \"".$msg['suite']."&nbsp;<img src='./images/search.gif' border='0' align='absmiddle'/>\";
				if(a.addEventListener){
					a.addEventListener('click',function(){
						document.search_authors.action='./index.php?lvl=more_results&tab=affiliate';
						document.search_authors.author_type.value = type;
						document.search_authors.submit();
						return false;
					},true);
				}else{
					a.addEvent('onclick',function(){
						document.search_authors.action='./index.php?lvl=more_results&tab=affiliate';
						document.search_authors.author_type.value = type;
						document.search_authors.submit();
						return false;
					});
				}
				span.appendChild(a);
				container.appendChild(span);
			}
		</script>
	</blockquote>";
	$form = "<div style=search_result><form name=\"search_authors\" action=\"./index.php?lvl=more_results\" method=\"post\">\n";
	if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
	$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">\n";
	$form .= "<input type=\"hidden\" name=\"mode\" value=\"auteur\">\n";
	$form .= "<input type=\"hidden\" name=\"author_type\" value=\"\">\n";
	$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_auteurs."\">\n";
	$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">\n";
	$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
	$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\"></form></div>\n";
	print $form;	
	print "</div>";
}else{
	if ($nb_result_auteurs) {
		print "<div id=\"auteur\" name=\"auteur\">";
		for($i=0;$i<count($titre_resume);$i++)  {
			if($i==1) print "<blockquote>";
			print "<strong>$titre_resume[$i]</strong> ".$nb_result_resume[$i]." $msg[results] ";
			// Le lien validant le formulaire est inséré avant le formulaire, cela évite les blancs à l'écran
			if($link_type_resume[$i]) {
				$clause_link=$clause." and author_type='".$link_type_resume[$i]."'";
			}else{
				$clause_link=$clause;
			}
			if ($nb_result_resume[$i]) {
				print "<a href=\"#\" onClick=\"
				document.forms.search_authors.count.value='".$nb_result_resume[$i]."';
				document.forms.search_authors.clause.value='".htmlentities(addslashes($clause_link),ENT_QUOTES,$charset)."';
				document.forms.search_authors.author_type.value='$link_type_resume[$i]';
				document.forms['search_authors'].submit(); return false;\">".$msg['suite']."&nbsp;<img src=./images/search.gif border='0' align='absmiddle'/></a>";
			}
			print "<br />";
		}
		if($i>1) print "</blockquote>";
		// tout bon, y'a du résultat, on lance le pataquès d'affichage
		$form = "<div style=search_result><form name=\"search_authors\" action=\"./index.php?lvl=more_results\" method=\"post\">\n";
		if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
		$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"mode\" value=\"auteur\">\n";
		$form .= "<input type=\"hidden\" name=\"author_type\" value=\"\">\n";
		$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_auteurs."\">\n";
		$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\"></form></div>\n";
		print $form;
		print "</div>";
	}
}

if ($nb_result_auteurs) {
	$_SESSION["level1"]["author"]["form"]=$form;
	$_SESSION["level1"]["author"]["count"]=$nb_result_auteurs;	
}

