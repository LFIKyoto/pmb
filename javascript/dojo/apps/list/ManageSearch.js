// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ManageSearch.js,v 1.1.6.1 2019-11-22 15:01:20 dgoron Exp $

define([
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/request",
        "dojo/query",
        "dojo/on",
        "dojo/dom-attr",
        "dojo/dom",
        "dojo/dom-style",
        "dojo/request/xhr",
        "dojo/ready"
], function(declare, lang, request, query, on, domAttr, dom, domStyle, xhr, ready){
	return declare(null, {
		objects_type:null,
		constructor: function(objects_type) {
			this.objects_type = objects_type;
			on(dom.byId(this.objects_type+'_search_img'), 'click', lang.hitch(this, this.contentShow));
			if(dom.byId(this.objects_type+'_applied_sort_more')) {
				on(dom.byId(this.objects_type+'_applied_sort_more'), 'click', lang.hitch(this, this.appliedSortMore));
			}
			var nodes = document.querySelectorAll("."+this.objects_type+"_applied_sort_delete");
			if(nodes.length) {
				for(var i=1; i<=nodes.length; i++) {
					on(dom.byId(this.objects_type+'_applied_sort_delete_'+i), 'click', lang.hitch(this, this.appliedSortDelete, i));
				}
			}
		},
		contentShow: function() {
			var domNode = dom.byId(this.objects_type+'_search_content');
			if(domStyle.get(domNode, 'display') == 'none') {
				domStyle.set(domNode, 'display', 'block');
				domAttr.set(dom.byId(this.objects_type+'_search_img'), 'src', pmbDojo.images.getImage('minus.gif'));
			} else {
				domStyle.set(domNode, 'display', 'none');
				domAttr.set(dom.byId(this.objects_type+'_search_img'), 'src', pmbDojo.images.getImage('plus.gif'));
			}
		},
		appliedSortMore: function() {
			var domNode = dom.byId(this.objects_type+'_applied_sort_more_content');
			var number = domAttr.get(domNode, 'data-applied-sort-number');
			// Limitons à 3 critères pour le moment
			if(number >= 3) {
				alert(pmbDojo.messages.getMessage('list', 'list_ui_sort_by_max_reached'));
				return;
			}
			xhr('./ajax.php?module=ajax&categ=list&sub=options&action=get_search_order_selector&objects_type='+this.objects_type+'&id='+number, {
				sync: false,
			}).then(lang.hitch(this, 
					function(response){
						var domNode = dom.byId(this.objects_type+'_applied_sort_more_content');
						var number = domAttr.get(domNode, 'data-applied-sort-number');
						domNode.innerHTML += response;
						number++;
						domAttr.set(domNode, 'data-applied-sort-number', number); 
					})
			);
		},
		appliedSortDelete: function(ind) {
			var domNode = dom.byId(this.objects_type+'_applied_sort_'+ind);
			domNode.innerHTML = '';
		}
	});
});