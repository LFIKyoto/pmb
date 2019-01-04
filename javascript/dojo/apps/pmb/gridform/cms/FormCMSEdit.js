// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormCMSEdit.js,v 1.1 2018-11-21 21:10:46 dgoron Exp $

define([
        'dojo/_base/declare',
        'dojo/_base/lang',
        'dojo/topic',
        'dojo/query',
        'dojo/on',
        'dojo/request',
        'dojo/dom-attr',
        'apps/pmb/gridform/FormEdit',
        ], function(declare, lang, topic, query, on, request, domAttr, FormEdit){
		return declare([FormEdit], {
			
			switchGrid: function(evt){
				this.flagOriginalFormat = true;
				this.destroyAjaxElements();
				this.unparseDom();
				cms_editorial_load_type_form(document.getElementById('cms_editorial_form_type').value, document.getElementById('cms_editorial_form_type'));
				this.getDefaultPos();
				this.getDatas();
			},
		})
});