/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categ_drop.js,v 1.1 2012-06-20 09:48:16 ngantier Exp $ */


/**********************************
 *								  *				
 *      Tri des categ              *
 *                                * 
 **********************************/
/*
 * Fonction pour trier les categ
 */
function categ_categ(dragged,target){
	
	var categ_id = dragged.getAttribute("id_categ");
	var categ_cible_id = target.getAttribute("id_categ");

	var categ=target.parentNode;
	categ.insertBefore(dragged,target);
	
	categ_downlight(target);
	
	recalc_recept();
	update_order(dragged,target);
}

/*
 * Mis à jour de l'ordre
 */
function update_order(source,cible){
	var src_order =  source.getAttribute("order");
	var target_order = cible.getAttribute("order");
	var categ = source.parentNode;
	
	var index = 0;
	var tab_categ_order = new Array();
	for(var i=0;i<categ.childNodes.length;i++){
		if(categ.childNodes[i].nodeType == 1){
			if(categ.childNodes[i].getAttribute("recepttype")=="categ"){
				categ.childNodes[i].setAttribute("order",index);
				tab_categ_order[index] = categ.childNodes[i].getAttribute("id").substr(5);
				index++;
			}
		}
	}
	if(document.getElementById("tab_categ_order")){
		document.getElementById("tab_categ_order").value=tab_categ_order.join(",");
	}	
	//var url= "./ajax.php?module=ajax&categ=tri&quoifaire=up_order_categ";
	//var action = new http_request();
	//action.request(url,true,"&tablo_categ="+tab_categ.join(","));
}

function categ_highlight(obj) {
	obj.style.background="#DDD";	
}

function categ_downlight(obj) {
	obj.style.background="";
}
