/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_composee_drag_n_drop.js,v 1.2 2014-08-08 13:29:24 apetithomme Exp $ */


/******************************************************************
 *																  *				
 *      Drag'n'drop des éléments d'une vedette composée        	  *
 *							 									  * 
 ******************************************************************/
/*
 * Fonction pour ajouter un nouvel élément
 */
function vedette_composee_available_fields_vedette_composee_subdivision(dragged,target){
	var subdivision_id = target.getAttribute("id");
	var elements_order = document.getElementById(subdivision_id + "_elements_order");
	var new_order;
	var nb_elements;
	
	if (elements_order.value) {
		var tab_elements_order = elements_order.value.split(",");
		nb_elements = tab_elements_order.length;
		new_order = get_max(tab_elements_order) + 1;
	} else {
		nb_elements = 0;
		new_order = 0;
	}
	vedette_element_downlight(target);
	
	if ((target.getAttribute("cardmax") == "") || (target.getAttribute("cardmax") > nb_elements)) {
		var div = document.createElement("div");
		div.setAttribute("id", subdivision_id + "_element_" + new_order);
		div.setAttribute("class", "vedette_composee_element");
		div.setAttribute("dragtype", "vedette_composee_element");
		div.setAttribute("recepttype", "vedette_composee_element");
		div.setAttribute("draggable", "yes");
		div.setAttribute("recept", "yes");
		div.setAttribute("order", new_order);
		div.setAttribute("highlight", "vedette_element_highlight");
		div.setAttribute("downlight", "vedette_element_downlight");
		div.setAttribute("handler", subdivision_id + "_element_" + new_order + "_handler");
		div.setAttribute("vedettetype", dragged.getAttribute("vedettetype"));
		
		var handler = document.createElement("span");
		handler.setAttribute("id", subdivision_id + "_element_" + new_order + "_handler");
		handler.setAttribute("style", "float:left;padding-right:7px;");
		
		var img = document.createElement("img");
		img.setAttribute("src", "./images/drag_symbol.png");
		img.setAttribute("style", "vertical-align:middle;");
		
		handler.appendChild(img);
		div.appendChild(handler);
		
		vedette_element.create_box(dragged.getAttribute("vedettetype"), div, target.getAttribute("subdivisiontype"), new_order);
		
		target.insertBefore(div, target.lastElementChild);
		vedette_composee_update_order(target);
		
		init_drag();
		ajax_pack_element(document.getElementById(subdivision_id + "_element_" + new_order + "_label"));
	} else {
		alert("Le nombre maximal d'elements pour cette subdivision est atteint !");
	}
}

/*
 * Fonction pour trier les éléments
 */
function vedette_composee_element_vedette_composee_element(dragged,target){
	var dragged_parent = dragged.parentNode;
	var target_parent = target.parentNode;
	
	vedette_element_downlight(target);
	
	// On commence par vérifier qu'on reste bien dans la même subdivision
	if (dragged_parent == target_parent) {
		dragged_parent.insertBefore(dragged, target);
		recalc_recept();
		vedette_composee_update_order(dragged_parent);
	} else {
		vedette_composee_element_vedette_composee_subdivision(dragged,target_parent);
	}
}

/*
 * Fonction pour changer de subdivision
 */
function vedette_composee_element_vedette_composee_subdivision(dragged,target){
	var dragged_parent = dragged.parentNode;
	var subdivision_id = target.getAttribute("id");
	var elements_order = document.getElementById(subdivision_id + "_elements_order");
	var new_order;
	var nb_elements;
	
	vedette_element_downlight(target);
	
	if (elements_order.value) {
		var tab_elements_order = elements_order.value.split(",");
		nb_elements = tab_elements_order.length;
		new_order = get_max(tab_elements_order) + 1;
	} else {
		nb_elements = 0;
		new_order = 0;
	}
	
	if ((target.getAttribute("cardmax") == "") || (target.getAttribute("cardmax") > nb_elements)) {
	// On vérifie qu'on change de subdivision, sinon on ne fait rien
		if (dragged_parent != target) {
			vedette_element.update_box(dragged.getAttribute("vedettetype"), dragged, target.getAttribute("subdivisiontype"), new_order);
			
			dragged.setAttribute("id", subdivision_id + "_element_" + new_order);
			dragged.setAttribute("order", new_order);
			
			var handler = document.getElementById(dragged.getAttribute("handler"));
			dragged.setAttribute("handler", subdivision_id + "_element_" + new_order + "_handler");
			handler.setAttribute("id", subdivision_id + "_element_" + new_order + "_handler");
			
			target.insertBefore(dragged, target.lastElementChild);
			recalc_recept();
			vedette_composee_update_order(dragged_parent);
			vedette_composee_update_order(target);
		}
	} else {
		alert("Le nombre maximal d'elements pour cette subdivision est atteint !");
	}
}

/*
 * Fonction supprimer un élément
 */
function vedette_composee_element_vedette_composee_delete_element(dragged,target){
	var parent = dragged.parentNode;
	
	parent.removeChild(dragged);
	recalc_recept();
	vedette_composee_update_order(parent);
	
	vedette_element_downlight(target);
}

/**
 * Mise à jour de l'ordre
 * 
 * @param parent Subdivision parente
 */
function vedette_composee_update_order(parent){
	var parent_id = parent.getAttribute("id");
	var subdivisionorder = parent.getAttribute("order");
	
	// On met à jour le tableau des libellés pour l'aperçu
	var id_tab_vedette_elements = parent_id.replace("_" + parent.getAttribute("subdivisiontype"), "") + "_tab_vedette_elements";
	window[id_tab_vedette_elements][subdivisionorder] = new Object();
	
	var index = 0;
	var elements_order = new Array();
	for(var i=0;i<parent.childNodes.length;i++){
		if(parent.childNodes[i].nodeType == 1){
			if(parent.childNodes[i].getAttribute("recepttype")=="vedette_composee_element"){
				elements_order[index] = parent.childNodes[i].getAttribute("order");
				
				var label = document.getElementById(parent.childNodes[i].getAttribute("id") + "_label").getAttribute("rawlabel");
				window[id_tab_vedette_elements][subdivisionorder][index] = label;
				
				index++;
			}
		}
	}
	var id_vedette_apercu = parent_id.replace("_" + parent.getAttribute("subdivisiontype"), "") + "_vedette_composee_apercu";
	var id_vedette_separator = parent_id.replace("_" + parent.getAttribute("subdivisiontype"), "") + "_separator";
	vedette_composee_update_apercu(id_vedette_apercu, window[id_tab_vedette_elements], window[id_vedette_separator]);
	
	if(document.getElementById(parent_id + "_elements_order")){
		document.getElementById(parent_id + "_elements_order").value=elements_order.join(",");
	}
}

/*
 * Mise à jour de l'aperçu
 */
function vedette_composee_update_apercu(id_apercu, tab_vedette_elements, vedette_separator) {
	apercu = "";
	
	for (var i in tab_vedette_elements) {
		for (var j in tab_vedette_elements[i]) {
			if (tab_vedette_elements[i][j]) {
				if (apercu) apercu = apercu + vedette_separator;
				apercu = apercu + tab_vedette_elements[i][j];
			}
		}
	}
	document.getElementById(id_apercu).value = apercu;
}

/**
 * Mise à jour de l'ensemble des éléments de la vedette composée
 */
function vedette_composee_update_all(id_vedette_composee_subdivisions) {
	var vedette_composee_subdivisions = document.getElementById(id_vedette_composee_subdivisions);
	for(var i=0;i<vedette_composee_subdivisions.childNodes.length;i++){
		if(vedette_composee_subdivisions.childNodes[i].nodeType == 1){
			if(vedette_composee_subdivisions.childNodes[i].getAttribute("recepttype")=="vedette_composee_subdivision"){
				vedette_composee_update_order(vedette_composee_subdivisions.childNodes[i]);
			}
		}
	}
}

function vedette_element_highlight(obj) {
	obj.style.background="#DDD";	
}

function vedette_element_downlight(obj) {
	obj.style.background="";
}

function get_max(array) {
	var max = 0;
	for (var i in array) {
		if (array[i] > max) {
			max  = array[i];
		}
	}
	return max*1;
}
