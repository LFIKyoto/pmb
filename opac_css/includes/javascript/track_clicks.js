// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: track_clicks.js,v 1.4 2015-11-24 14:03:26 arenou Exp $

function _trackClick(event){
	var el = event.srcElement || event.target;
	while (el && (typeof el.tagName == 'undefined' || el.tagName.toLowerCase() != 'a' || !el.href)) {
		el = el.parentNode;
	}
	if (el && el.href) {
		var open_link = false;

		if (el.href.indexOf(location.host) == -1 && el.href.substring(0,10).toLowerCase()!='javascript') {
			var type = 'external_url_external';
			if (el.getAttribute('type')) {
				type = el.getAttribute('type');
			}
			log_click(el.getAttribute('href'),type);
			open_link = true;
		}
		//L'appel est synchrone et le comportement doit rester à l'identique
//		if (open_link) {
//			setTimeout(function() {
//				window.open(el.href, '_blank');
//			}.bind(el), 500);
//			event.preventDefault ? event.preventDefault() : event.returnValue = false;
//		}
	}
}

function log_click(url,type){
	
	var action = new http_request();
	var ajax_url = './ajax.php?module=ajax&categ=log&type_url='+type+'&called_url='+url;
	
	action.request(ajax_url);
	
}

if (window.addEventListener) {
	window.addEventListener('load', function() {
		document.body.addEventListener('click', _trackClick, false);
	}, false);
}
else {
	window.attachEvent && window.attachEvent('onload', function() {
		document.body.attachEvent('onclick', _trackClick);
	});
}