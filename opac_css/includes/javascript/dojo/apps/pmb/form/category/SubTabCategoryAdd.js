// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabCategoryAdd.js,v 1.2 2019-07-17 14:29:12 dgoron Exp $


define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request/xhr',
        'dojo/dom-form',
        'dijit/layout/TabContainer',
        'dojox/layout/ContentPane',
        'dojo/query',
        'dojo/ready',
        'dojo/topic',
        'dijit/registry',
        'dojo/dom-attr',
        'dojo/dom-geometry',
        'dojo/dom-construct',
        'dojo/dom-style',
        'dojo/_base/xhr',
        'apps/pmb/gridform/FormEdit',
        'dojo/dom-form',
        'dojo/request/iframe',
        'dojo/io-query',
        'apps/pmb/form/SubTabAdd',
        'apps/pmb/form/FormController',
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, xhr, FormEdit, domForm, iframe, ioQuery, SubTabAdd, FormController){
		return declare([SubTabAdd], {
			onDownloadEnd: function(){
				var domNode = dom.byId('id_thes');
				domAttr.set(domNode, 'onchange', '');
				on(domNode, 'change', lang.hitch(this, this.changeThesaurus, domNode));
				this.inherited(arguments);
			},
			changeThesaurus: function(domNode) {
				if(confirm(pmbDojo.messages.getMessage('grid', 'category_change_thesaurus_confirm'))) {
					return true;	
				} else {
					return false;
				}
			},
		})
});