/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: circdiff_tpl_drop.js,v 1.1 2014-10-14 10:41:02 dgoron Exp $ */

function circdiff_highlight(obj) {
	obj.style.background="#DDD";
	
}
function circdiff_downlight(obj) {
	obj.style.background="";
}

function circdiffprint_circdiffprint(dragged,target){
	
	var circdiff_id = dragged.getAttribute("id_circdiffprint");
	var circdiff_cible_id = target.getAttribute("id_circdiffprint");

	var circdiff=target.parentNode;
	circdiff.insertBefore(dragged,target);
	
	circdiff_downlight(target);
	
	recalc_recept();
	update_orderprint(dragged,target);
}

function update_orderprint(source,cible){
	var src_order =  source.getAttribute("order");
	var target_order = cible.getAttribute("order");
	var circdiff = source.parentNode;
	
	var index = 0;
	var tab_circdiff = new Array();
	for(var i=0;i<circdiff.childNodes.length;i++){
		if(circdiff.childNodes[i].nodeType == 1){
			circdiff.childNodes[i].setAttribute("order",index);
			if(circdiff.childNodes[i].getAttribute("id_circdiff")){
				tab_circdiff[index] = circdiff.childNodes[i].getAttribute("id_circdiff");
			}
			index++;
		}
	}
	document.getElementById('order_tpl').value = tab_circdiff.join(",");
	var id_serialcirc = source.getAttribute("id_serialcirc");	
	var url= "./ajax.php?module=edit&categ=serialcirc_diff&sub=up_order_circdiffprint&id_serialcirc="+id_serialcirc;	
	var action = new http_request();
	action.request(url,true,"&tablo="+tab_circdiff.join(","));
}
