// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Source.js,v 1.5 2015-02-25 11:51:41 arenou Exp $


define(["dojo/_base/declare", "dojo/parser", "dojo/topic", "dojo/_base/lang", "dojo/dom", "dijit/form/Form", "dojo/dom-construct", "dojo/dom-form"], function(declare, parser, topic, lang, dom, Form, domConstruct, domForm){
	return declare([Form], {
		currentSelector: "",
		
		startup: function(){
			this.inherited(arguments);
			var children = this.getChildren();
			for(var i=0 ; i<children.length ; i++){
				if(children[i].get("id") == "selector_choice"){
					children[i].onChange = lang.hitch(this,this.loadSelectorForm);
  				}
				if(children[i].get("id") == "docwatch_datasource_form_delete") {
					children[i].onClick = lang.hitch(this,this.deleteSource);
				}
			}
			domConstruct.create('input',{
				type: "hidden",
				name: "num_watch",
				value: this.getParent().get("watchId")
			},this.domNode);
			
			domConstruct.create('input',{
				type: "hidden",
				name: "id_datasource",
				value: this.getParent().get("sourceId")
			},this.domNode);
			
			domConstruct.create('input',{
				type: "hidden",
				name: "className",
				value: this.getParent().get("className")
			},this.domNode);
		},
		
		loadSelectorForm: function(selectorClassName){
			if(selectorClassName != this.currentSelector){
				this.currentSelector = selectorClassName;
				var content = dijit.byId("selector_content");
				if(content){
					if((confirm(pmbDojo.messages.getMessage("dsi","docwatch_switch_selector_confirm")))){
						content.href = "./ajax.php?module=dsi&categ=docwatch&sub=sources&action=get_selector_form&class="+this.currentSelector;
						content.refresh();
					}
				}
			}
		},
		
		deleteSource : function(){
			if(confirm(pmbDojo.messages.getMessage("dsi","docwatch_datasource_delete_confirm"))){
				topic.publish("source","deleteSource",{
				sourceId: this.getParent().get("sourceId")
				});
			}
			
		},
				
		onSubmit: function(){
			if(this.isValid()){
				topic.publish("source","saveSource",domForm.toObject(this.containerNode));
			}
			return false;
		}
	});
});