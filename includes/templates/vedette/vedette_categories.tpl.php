<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_categories.tpl.php,v 1.3 2014-08-13 09:53:57 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $vedette_categories_tpl, $msg;

$vedette_categories_tpl['vedette_categories_selector']='
<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_form" class="vedette_composee_element_form">
	<input id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label" class="saisie-20emr" type="text" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][label]" autfield="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id" completion="categories_mul" autocompletion="on" autocomplete="off" vedettetype="vedette_categories" callback="vedette_composee_callback" value="!!vedette_element_label!!" rawlabel="!!vedette_element_rawlabel!!"/>
	<input class="bouton" type="button" onclick="openPopUp(\'./select.php?what=categorie&dyn=4&caller=!!caller!!&p1=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id&p2=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label&callback=vedette_composee_callback&infield=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label&deb_rech=\'+encodeURIComponent(document.getElementById(\'!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label\').value), \'select_author0\', 500, 400, -2, -2, \'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes\')" value="...">
	<input id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id" type="hidden" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][id]" value="!!vedette_element_id!!" />
	<input type="hidden" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][type]" value="vedette_categories" />
</div>
';

$vedette_categories_tpl['vedette_categories_script']='
var vedette_categories = {
	// parent : parent direct du selecteur
	// vedette_composee_subdivision_id : id de la subdivision parente
	// vedette_composee_element_order : ordre de l\'element
	create_box : function(parent, vedette_composee_subdivision_id, vedette_composee_element_order, id, label, rawlabel) {
		var form = document.createElement("div");
		form.setAttribute("id", "!!caller!!_!!property_name!!_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_form");
		form.setAttribute("name", "!!caller!!_!!property_name!!_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order+ "_form");
		form.setAttribute("class", "vedette_composee_element_form");
		
		var text = document.createElement("input");
		text.setAttribute("id", "!!caller!!_!!property_name!!_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label");
		text.setAttribute("class", "saisie-20emr");
		text.setAttribute("type", "text");
		text.setAttribute("name", "!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][label]");
		text.setAttribute("autfield", "!!caller!!_!!property_name!!_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id");
		text.setAttribute("completion", "categories_mul");
		text.setAttribute("autocompletion", "on");
		text.setAttribute("autocomplete", "off");
		text.setAttribute("placeholder", "['.$msg["vedette_categories"].']");
		text.setAttribute("vedettetype", "vedette_categories");
		if (label) {
			text.setAttribute("value", label);
		}
		if (rawlabel) {
			text.setAttribute("rawlabel", rawlabel);
		}
		text.setAttribute("callback", "vedette_composee_callback");
			
		var select = document.createElement("input");
		select.setAttribute("id", "!!caller!!_!!property_name!!_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_select");
		select.setAttribute("class", "bouton");
		select.setAttribute("type", "button");
		select.addEventListener("click", function(e){
			var deb_rech = document.getElementById("!!caller!!_!!property_name!!_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label").value;
			openPopUp("./select.php?what=categorie&dyn=4&caller=!!caller!!&p1=!!caller!!_!!property_name!!_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id&p2=!!caller!!_!!property_name!!_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label&callback=vedette_composee_callback&infield=!!caller!!_!!property_name!!_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label&deb_rech=" + encodeURIComponent(deb_rech), "select_author0", 500, 400, -2, -2, "scrollbars=yes, toolbar=no, dependent=yes, resizable=yes");
		}, false);
		select.setAttribute("value", "...");
		
		var element_id = document.createElement("input");
		element_id.setAttribute("id", "!!caller!!_!!property_name!!_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id");
		element_id.setAttribute("type", "hidden");
		element_id.setAttribute("name", "!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][id]");
		if (id) {
			element_id.setAttribute("value", id);
		}
		
		var element_type = document.createElement("input");
		element_type.setAttribute("type", "hidden");
		element_type.setAttribute("name", "!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][type]");
		element_type.setAttribute("value", "vedette_categories");
		
		form.appendChild(text);
		form.appendChild(select);
		form.appendChild(element_id);
		form.appendChild(element_type);
		parent.appendChild(form);
	},
	
	callback : function(id) {
		document.getElementById(id).setAttribute("rawlabel", document.getElementById(id).value);
		document.getElementById(id).value = "['.$msg["vedette_categories"].'] " + document.getElementById(id).value;
	}
}
';