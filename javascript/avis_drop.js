/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis_drop.js,v 1.2 2012-10-18 14:15:25 dgoron Exp $ */


/**********************************
 *								  *				
 *      Tri des avis              *
 *                                * 
 **********************************/
/*
 * Fonction pour trier les avis
 */
function avisdrop_avisdrop(dragged,target){
	
	var avis_id = dragged.getAttribute("id_avis");
	var avis_cible_id = target.getAttribute("id_avis");

	var avis=target.parentNode;
	avis.insertBefore(dragged,target);
	
	avis_downlight(target);
	
	recalc_recept();
	update_order_avis(dragged,target);
}


/*
 * Mis à jour de l'ordre
 */
function update_order_avis(source,cible){
	var src_order =  source.getAttribute("order");
	var target_order = cible.getAttribute("order");
	var avis = source.parentNode;
	
	var index = 0;
	var tab_avis = new Array();
	for(var i=0;i<avis.childNodes.length;i++){
		if(avis.childNodes[i].nodeType == 1){
			avis.childNodes[i].setAttribute("order",index);
			if(avis.childNodes[i].getAttribute("id_avis")){
				tab_avis[index] = avis.childNodes[i].getAttribute("id_avis");
			}
			index++;
		}
	}
	
	var url= "./ajax.php?module=ajax&categ=tri&quoifaire=up_order_avis";
	var action = new http_request();
	action.request(url,true,"&tablo_avis="+tab_avis.join(","));
}


function avis_highlight(obj) {
	obj.style.background="#DDD";
	
}
function avis_downlight(obj) {
	obj.style.background="";
}
