// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Dialog.js,v 1.5 2015-02-20 16:13:37 vtouchard Exp $


define(["dojo/_base/declare", "dojo/topic", "dojo/_base/lang", "dijit/Dialog", "apps/docwatch/form/Category", "apps/docwatch/form/Watch"], function(declare, topic, lang, Dialog, CategoryForm, WatchForm){
	return declare([Dialog], {
		postCreate: function(){
			this.inherited(arguments);
			this.own(
				topic.subscribe("watchesUI",lang.hitch(this,this.handleEvents)),
				topic.subscribe("watchStore",lang.hitch(this,this.handleEvents))
			);
		},
		
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case "showCategoryForm" :
					if(evtArgs.values.id){
						this.set("title", pmbDojo.messages.getMessage("dsi","dsi_js_docwatch_edit_category"));
					}else{
						this.set("title", pmbDojo.messages.getMessage("dsi","dsi_js_docwatch_add_category"));
					}
					this.destroyDescendants();
					this.addChild(new CategoryForm({
						categories: evtArgs.categories,
						values: evtArgs.values 
					}));
					this.show();
					break;
				case "showWatchForm" :
					if(evtArgs.values.id){
						this.set("title", pmbDojo.messages.getMessage("dsi","dsi_js_docwatch_edit_watch"));
					}else{
						this.set("title", pmbDojo.messages.getMessage("dsi","dsi_js_docwatch_add_watch"));
					}
					this.destroyDescendants();
					this.addChild(new WatchForm({
						categories: evtArgs.categories,
						values: evtArgs.values 
					}));
					this.show();
					break;
				case "categorySaved" :
				case "categoryDeleted" :
				case "watchSaved" :
				case "watchDeleted" :
					this.destroyDescendants();
					this.hide();
					break;
			}
		}
	});
});