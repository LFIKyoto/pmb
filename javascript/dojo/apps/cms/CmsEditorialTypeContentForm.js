// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CmsEditorialTypeContentForm.js,v 1.4 2018-11-21 21:10:46 dgoron Exp $


define([
        'dojo/_base/declare',
        'dojox/layout/ContentPane',
        'apps/pmb/gridform/cms/FormCMSEdit',
        ], function(declare, ContentPane, FormCMSEdit){
		return declare([ContentPane], {
			type:null,
			activated_grid:null,
			formCMSEdit:null,
			constructor: function(data) {
				this.type = data.type;
				this.activated_grid = data.activated_grid;
			},
			onLoad: function(){
				if(this.activated_grid && !this.formCMSEdit) {
					this.formCMSEdit = new FormCMSEdit('cms', this.type);
				}
				ajax_parse_dom();
				init_drag();
			},
		})
});