// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Watch.js,v 1.7 2015-03-09 13:53:58 vtouchard Exp $


define(["dojo/_base/declare", "dojo/parser", "dojo/topic", "dojo/_base/lang", "dojo/dom", "dijit/form/Form", "dojo/dom-form", "dojo/text!pmbBase/ajax.php?module=dsi&categ=docwatch&sub=forms&action=get_form&form=docwatch_watch_form_tpl", "dojo/query"], function(declare, parser, topic, lang, dom, Form, domForm, template, query){
	return declare([Form], {
		templateString: template,
		categories : [],
		values: {},
		
		postCreate: function(){
			this.inherited(arguments);
			parser.parse(this.containerNode);
			var children = this.getChildren();
			for(var i=0 ; i<children.length ; i++){
				switch(children[i].get("id")){
				case "parent":
					for(var j=0 ; j<this.categories.length ; j++){
						if(this.categories[j].value == this.values.parent_category){
							this.categories[j].selected = true;
							break;
						}
					}
					children[i].addOption(this.categories);
					break;
				case "title":
					if(this.values.title){
						children[i].set("value",this.values.title);
					}
					break;
				case "ttl":
					if(this.values.ttl){
						children[i].set("value",this.values.ttl);
					}
					break;
				case "desc":
					if(this.values.desc){
						children[i].set("value",this.values.desc);
					}
					break;
				case "logo_url":
					if(this.values.logo_url){
						children[i].set("value",this.values.logo_url);
					}
					break;
				case "docwatch_form_delete":
					if(this.values.id){
						children[i].on("click",lang.hitch(this,this.deleteWatch));
					}else{
						children[i].destroy();	
					}
					break;
				case "record_types":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.record_default_type){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				case "record_status":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.record_default_status){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				case "article_type":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.article_default_content_type){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				case "article_status":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.article_default_publication_status){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				case "article_parent":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.article_default_parent){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				case "section_type":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.section_default_content_type){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				case "section_status":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.section_default_publication_status){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				case "section_parent":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.section_default_parent){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				}
			}
			//Édition d'une veille. En création on ne passeras pas dans le if
			if(this.values.allowed_users){
				var checkboxes = query('input[type="checkbox"]', this.containerNode);
				for(var i=0 ; i<checkboxes.length ; i++){
					if(checkboxes[i].name == "allowed_users[]" && this.values.allowed_users.indexOf(checkboxes[i].value)!=-1){
						checkboxes[i].checked = true;
					}
				}	
			}
		},
		onSubmit: function(){
			//on met l'id si défini...
			if(this.values.id){
				dom.byId("id").value = this.values.id;
			}
			if(this.isValid()){
				topic.publish("watch","saveWatch",domForm.toObject(this.containerNode));
			}
			return false;
		},
		deleteWatch:function(){
			if(confirm(pmbDojo.messages.getMessage("dsi","docwatch_confirm_watch_delete"))){
				topic.publish("watch","deleteWatch",{
					watchId: this.values.id}
				);
			}
		},
	});
});