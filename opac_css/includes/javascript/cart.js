// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart.js,v 1.1.2.1 2015-08-13 14:01:13 jpermanne Exp $

function changeBasketImage(id_notice) {
	
	if(window.parent.document.getElementById('baskets'+id_notice)) {
		var basket_node = window.parent.document.getElementById('baskets'+id_notice);
		if (basket_node.hasChildNodes()) {
				basket_node.removeChild(basket_node.firstChild);
		}
		var basket_link = window.parent.document.createElement('a');
		basket_link.setAttribute('href','#');
		basket_link.setAttribute('class','img_basket_exist');
		basket_link.setAttribute('title',msg_notice_title_basket_exist);
		basket_node.appendChild(basket_link);
		var basket_img = window.parent.document.createElement('img');
		basket_img.setAttribute('src','./images/basket_exist.gif');
		basket_img.setAttribute('alt',msg_notice_title_basket_exist);
		basket_link.appendChild(basket_img);
	}
}