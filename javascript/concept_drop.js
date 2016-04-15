/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: concept_drop.js,v 1.1 2014-07-29 10:53:08 apetithomme Exp $ */


/**********************************
 *								  *				
 *      Tri des concepts          *
 *                                * 
 **********************************/
/*
 * Fonction pour trier les concepts
 */
function concept_concept(dragged,target){
	var concept_id = dragged.getAttribute("id_concept");
	var concept_cible_id = target.getAttribute("id_concept");

	var concept=target.parentNode;
	concept.insertBefore(dragged,target);
	
	concept_downlight(target);
	
	recalc_recept();
	update_order(dragged,target);
}

/*
 * Mis à jour de l'ordre
 */
function update_order(source,cible){
	var src_order =  source.getAttribute("order");
	var target_order = cible.getAttribute("order");
	var concept = source.parentNode;
	
	var index = 0;
	var tab_concept_order = new Array();
	for(var i=0;i<concept.childNodes.length;i++){
		if(concept.childNodes[i].nodeType == 1){
			if(concept.childNodes[i].getAttribute("recepttype")=="concept"){
				concept.childNodes[i].setAttribute("order",index);
				tab_concept_order[index] = concept.childNodes[i].getAttribute("id").substr(8);
				index++;
			}
		}
	}
	if(document.getElementById("tab_concept_order")){
		document.getElementById("tab_concept_order").value=tab_concept_order.join(",");
	}
	//var url= "./ajax.php?module=ajax&concept=tri&quoifaire=up_order_concept";
	//var action = new http_request();
	//action.request(url,true,"&tablo_concept="+tab_concept.join(","));
}

function concept_highlight(obj) {
	obj.style.background="#DDD";	
}

function concept_downlight(obj) {
	obj.style.background="";
}
