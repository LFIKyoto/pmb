// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb.js,v 1.1.2.1 2019-11-07 14:25:49 ngantier Exp $


function pnb_post_loan_info(notice_id) {
	id = notice_id;
	document.getElementById('response_pnb_pret_' + notice_id).innerHTML = "<div style='width:100%; height:30px;text-align:center'><img style='padding 0 auto; border:0px;' src='images/patience.gif' id='collapseall'></div>";		
	var request = new http_request();
	request.request('./ajax.php?module=ajax&categ=pnb&action=post_loan_info&notice_id=' + notice_id, false,'', true, function(data) {
	    var responce = JSON.parse(data);
	    document.getElementById('response_pnb_pret_' + notice_id).innerHTML = responce.message;
	    if (responce.infos && responce.infos.link && responce.infos.link.url) { 
	        window.open(responce.infos.link.url, '_blank'); 
	    }
	});
}