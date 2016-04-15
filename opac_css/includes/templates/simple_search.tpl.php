<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: simple_search.tpl.php,v 1.44 2014-12-18 08:21:15 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], "simple_search.tpl.php")) die("no access");

// template for PMB OPAC
switch ($search_type) {
	// éléments pour la recherche simple
	case "simple_search":
		$search_input = "
			<div id=\"search\">\n
			<ul class='search_tabs'>
				<li id='current'>".$msg["simple_search"]."</li>
				!!others!!".
				($opac_show_onglet_help ? "<li><a href=\"./index.php?lvl=infopages&pagesid=$opac_show_onglet_help\">".$msg["search_help"]."</a></li>": '')."
			</ul>
			<div id='search_crl'></div>\n
			<p class=\"p1\"><span>$msg[simple_search_tpl_text]</span></p>\n
			<div class='row'>\n
			<form name='search_input' action='!!action_simple_search!!' method='post' onSubmit=\"if (search_input.user_query.value.length == 0) { search_input.user_query.value='*'; return true; }\">\n
				<!--!!typdoc_field!!-->\n
				<input type='hidden' name='surligne' value='!!surligne!!'/>";
		if($opac_simple_search_suggestions){
			$search_input.= "
				<script type='text/javascript' src='$include_path/javascript/ajax.js'></script>
				<input type='text' name='user_query' id='user_query_lib' class='text_query' value=\"!!user_query!!\" size='65' expand_mode='2' completion='suggestions' word_only='no'/>\n";
		}else{
			$search_input.= "
				<input type='text' name='user_query' class='text_query' value=\"!!user_query!!\" size='65' />\n";
		}	
				
		$search_input.= "		
				<input type='submit' name='ok' value='".$msg["142"]."' class='boutonrechercher'/>\n";
		if ($opac_show_help) $search_input .= "<input type='button' value='$msg[search_help]' class='bouton' onClick='window.open(\"$base_path/help.php?whatis=simple_search\", \"search_help\", \"scrollbars=yes, toolbar=no, dependent=yes, width=400, height=400, resizable=yes\"); return false' />\n";
		$search_input .= "		<!--!!ou_chercher!!-->\n";
		
		if($opac_map_activate) 
		$search_input .= "			
				<div class='row'>
					<label class='etiquette'>".$msg["map_search"]."</label>
				</div>
				<div class='row'>
					!!map!!
				</div>";
		
		$search_input .= "		</form>\n
			</div>\n
			<script type='text/javascript' src='$include_path/javascript/ajax.js'></script>
			<script type='text/javascript'>\n
				document.search_input.user_query.focus();\n
				".($opac_simple_search_suggestions ? "ajax_parse_dom();" : "")."
			</script>\n	
		</div>";
		break;
	case "external_search":
		$search_input = "
			<ul class='search_tabs'>
				!!others!!
				<li id='current'>".$msg["connecteurs_external_search"]."</li>".
				($opac_show_onglet_help ? "<li><a href=\"./index.php?lvl=infopages&pagesid=$opac_show_onglet_help\">".$msg["search_help"]."</a></li>": '')."
			</ul>
			<div id='search_crl'></div>\n
			<p class=\"p1\"><span>".sprintf($msg["connecteurs_search_multi"],"./index.php?search_type_asked=external_search&external_type=multi")."</span></p>\n
			<div class='row'>\n
			<form name='search_input' action='./index.php?lvl=search_result&search_type_asked=external_search' method='post' onSubmit=\"if (search_input.user_query.value.length == 0) { search_input.user_query.value='*'; return true; }\">\n
				<!--!!typdoc_field!!--><br />\n
				<input type='hidden' name='surligne' value='!!surligne!!'/>
				<input type='text' name='user_query' class='text_query' value=\"!!user_query!!\" size='65' />\n
				<input type='submit' name='ok' value='".$msg["142"]."' class='boutonrechercher'/>\n";
			if ($opac_show_help) $search_input .= "<input type='button' value='$msg[search_help]' class='bouton' onClick='window.open(\"$base_path/help.php?whatis=simple_search\", \"search_help\", \"scrollbars=yes, toolbar=no, dependent=yes, width=400, height=400, resizable=yes\"); return false' />\n";
			$search_input .= "<!--!!ou_chercher!!-->\n
				<br /><a href='javascript:expandAll()'><img class='img_plusplus' src='./images/expand_all.gif' border='0' id='expandall'></a>&nbsp;<a href='javascript:collapseAll()'><img class='img_moinsmoins' src='./images/collapse_all.gif' border='0' id='collapseall'></a>
				<div id='external_simple_search_zone'><!--!!sources!!--></div>
			</form>\n
			</div>\n
			<script type='text/javascript'>\n
				document.search_input.user_query.focus();\n
				
			function change_source_checkbox(changing_control, source_id) {
				var i=0; var count=0;
				onoff = changing_control.checked;
				for(i=0; i<document.search_input.elements.length; i++)
				{
					if(document.search_input.elements[i].name == 'source[]')	{
						if (document.search_input.elements[i].value == source_id)
							document.search_input.elements[i].checked = onoff;
					}
				}	
			}
				</script>\n	";
		break;
	case "tags_search":
		$search_input = "
		<div id=\"search\">\n
			<ul class='search_tabs'>!!others!!".
				($opac_show_onglet_help ? "<li><a href=\"./index.php?lvl=infopages&pagesid=$opac_show_onglet_help\">".$msg["search_help"]."</a></li>": '')."
			</ul>
			<div id='search_crl'></div>\n
			<p class=\"p1\"><span>$msg[tags_search_tpl_text]</span></p>\n
			<div class='row'>\n
			<form name='search_input' action='./index.php?lvl=search_result&search_type_asked=tags_search' method='post' onSubmit=\"if (search_input.user_query.value.length == 0) { search_input.user_query.value='*'; return true; }\">\n
				<!--!!typdoc_field!!--><br />\n
				<input type='text' name='user_query' class='text_query' value=\"!!user_query!!\" size='65' />\n
				<input type='submit' name='ok' value='".$msg["142"]."' class='boutonrechercher'/>\n
			</form>\n
			</div>\n
			<script type='text/javascript'>\n
				document.search_input.user_query.focus();\n
				</script>\n	
		</div>";
		break;

	case "connect_empr":
		$search_input = "
			<div id=\"search\">\n
				<ul class='search_tabs'>!!others!!".
					($opac_show_onglet_help ? "<li><a href=\"./index.php?lvl=infopages&pagesid=$opac_show_onglet_help\">".$msg["search_help"]."</a></li>": '')."
				</ul><div id='search_crl'></div>\n
				<p class=\"p1\">&nbsp;</p>\n
				<div class='row'>\n
				!!account_or_form_empr_connect!!
				</div>\n
			</div>";
		break;		
	case "search_perso":
		$search_input = "
			<div id=\"search\">\n
				<ul class='search_tabs'>!!others!!".
					($opac_show_onglet_help ? "<li><a href=\"./index.php?lvl=infopages&pagesid=2\">".$msg["search_help"]."</a></li>": '')."
				</ul>
				<div id='search_crl'></div>\n
				<p class=\"p1\">&nbsp;</p>\n
				<div class='row'>\n
				!!contenu!!
				</div>\n
			</div>";
	case "perio_a2z":
		$search_input = "
			<div id=\"search\">\n
				<ul class='search_tabs'>!!others!!".
					($opac_show_onglet_help ? "<li><a href=\"./index.php?lvl=infopages&pagesid=$opac_show_onglet_help\">".$msg["search_help"]."</a></li>": '')."
				</ul><div id='search_crl'></div>\n
				<p class=\"p1\">&nbsp;</p>\n
				<div class='row'>\n
				!!contenu!!
				</div>\n
			</div>";
		break;		
	case "map":	
	//Géolocalisation
		$search_input = "
		<div id=\"search\">\n
			<ul class='search_tabs'>!!others!!".
			($opac_show_onglet_help ? "<li><a href=\"./index.php?lvl=infopages&pagesid=$opac_show_onglet_help\">".$msg["search_help"]."</a></li>": '')."
			</ul><div id='search_crl'></div>\n
			<p class=\"p1\">&nbsp;</p>\n
			<div class='row'>\n
			!!contenu!!
			</div>\n
		</div>";
	$search_form_map = "
		<script src='javascript/ajax.js'></script>
		<script type='text/javascript'>
		
		
		function test_form(form) {
		
			if ((form.categ_query.value.length == 0) && (form.all_query.value.length == 0) && ((form.concept_query && form.concept_query.value.length == 0) || (!form.concept_query)) ) {
			//	form.all_query.value='*';
				return true;
			}
		}
		</script>
		<form class='form-$current_module' id='search_form_map' name='search_form_map' method='post' action='./index.php?lvl=search_result&search_type_asked=tags_search' onSubmit='return test_form(this)'>
		
		<div class='form-contenu'>
		
		<table class='map_search'><tr><td>
		
		<div class='row'>
			<label class='etiquette' for='all_query'>$msg[global_search]</label>
		</div>
		<div class='colonne'>
			<div class='row'>
				<input class='saisie-80em' type='text' value='!!all_query!!' name='all_query' id='all_query' />
			</div>
		</div>
		!!docnum_query!!
		
		<div class='row'>
			<label class='etiquette' for='categ_query'>".$msg["search_categorie_title"]."</label>
		</div>
		<div class='colonne'>
			<div class='row'>
				<input class='saisie-80em' id='categ_query' type='text' value='!!categ_query!!' size='36' name='categ_query' autfield='categ_query' completion='categories_mul' autocomplete='off' />
			</div>
		</div>
		!!auto_postage!!
		";
		if($thesaurus_concepts_active){
			$search_form_map .= "
			<div class='row'>
				<label class='etiquette' for='concept_query'>".$msg["search_concept_title"]."</label>
			</div>
			<div class='colonne'>
				<div class='row'>
					<input class='saisie-80em' id='concept_query' type='text' value='!!concept_query!!' size='36' name='concept_query' autfield='concept_query' completion='onto' autocomplete='off' att_id_filter='http://www.w3.org/2004/02/skos/core#Concept' />
				</div>
			</div>";
		}
		$search_form_map .= "
		<div class='row'>
			<label class='etiquette' for='map_echelle_query'>".$msg["map_echelle"]."</label>
		</div>
		<div class='row'>
			!!map_echelle_list!!
		</div>
		<div class='row'>
			<label class='etiquette' for='map_projection_query'>".$msg["map_projection"]."</label>
		</div>
		<div class='row'>
			!!map_projection_list!!
		</div>		
		<div class='row'>
			<label class='etiquette' for='map_ref_query'>".$msg["map_ref"]."</label>
		</div>
		<div class='row'>
			!!map_ref_list!!
		</div>				
		<div class='row'>
			<label class='etiquette' for='map_equinoxe_query'>".$msg["map_equinoxe"]."</label>
		</div>
		<div class='row'>
			<input id='map_equinoxe_query' class='saisie-80em' type='text' value='!!map_equinoxe_value!!' name='map_equinoxe_query'>
		</div>
		<div class='row'>
			<span class='saisie-contenu'>
				$msg[155]&nbsp;<a class='aide' title='$msg[1900]$msg[1901]$msg[1902]' href='./help.php?whatis=regex' onclick='aide_regex();return false;'>$msg[1550]</a>
			</span>
		</div>
		<div class='colonne2'>
			<div class='row'>
				<label for='typdoc-query'>$msg[17]$msg[1901]</label>
			</div>
			<select id='typdoc-query' name='typdoc_query'>
			!!typdocfield!!
			</select>
		</div>
		<div class='colonne_suite'>
			<div class='row'>
				<label for='statut-query'>$msg[noti_statut_noti]</label>
			</div>
			<select id='statut-query' name='statut_query'>
			!!statutfield!!
			</select>
		</div>
		</td>
		<td>
		<div class='row'>
			<label class='etiquette'>".$msg["map_search"]."</label>
		</div>
		<div class='row'>
			!!map!!
		</div>
		</td>
		</tr>
		</table>
		<div class='row'>&nbsp;</div>
		</div>
		<!--	Bouton Rechercher	-->
		<div class='row'>
			<input type='submit' class='bouton' value='$msg[142]' />
		</div>
		<input type='hidden' name='etat' value='first_search'/>
		</form>
		
		<script type='text/javascript'>
		document.forms['search_form_map'].elements['all_query'].focus();
		ajax_parse_dom();
		</script>
	";
		break;		
}