// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_workshop_ui.js,v 1.14 2015-02-09 14:45:18 dgoron Exp $


define(["dojo/_base/declare", "apps/nomenclature/nomenclature_instruments_list_ui", "apps/nomenclature/nomenclature_instruments_list", "dojo/on", "dojo/dom-construct", "dojo/topic", "dojo/_base/lang", "dijit/registry","dijit/_WidgetBase"], function(declare, Instruments_list_ui, Instruments_list, on, domConstruct, topic, lang, registry, _WidgetBase){
	/*
	 *Classe nomenclature_workshop_ui. Classe générant la partie du formulaire liée a un atelier
	 */
	  return declare("nomenclature_workshop_ui",[_WidgetBase], {
			
			id:null,    
		  	workshop:null,
		  	dom_node:null,
		  	instruments_list_ui:null,
		  	instruments_list_node:null,
		  	abbreviation_node:null,
		  	dom_construct_id:null,
		  	indice:0,
		  	label:null,
		  	inputs_array:null,
		  	
		    constructor: function(params){
		    	this.inputs_array = new Array();
		    	this.own(topic.subscribe("workshop_ui",lang.hitch(this, this.handle_events)));
		    	this.own(topic.subscribe("instrument_ui", lang.hitch(this, this.handle_events)));
		    	this.own(topic.subscribe('instruments_list_ui', lang.hitch(this, this.handle_events)));
		    },
			
		    handle_events : function(evt_type,evt_args){
		    	switch(evt_type){
			    	case "instru_changed" :
			    		if(evt_args.hash.indexOf(this.workshop.get_hash())!=-1){
			    			this.maj_abbreviation();
			    		}
		    			break;
		    		case "instruments_list_ui_ready" : 
		    			if(evt_args.hash.indexOf(this.workshop.get_hash())!=-1){
		    				this.generate_inputs();
			    		}
		    			break;
		    	}
		    },
		    
		    buildRendering: function(){ 
		    	this.inherited(arguments);
		    	this.build_form();
		    },
		    
		    build_form: function(){
		    	this.set_dom_construct_id(this.get_dom_node().id+'_nomenclature_form_workshop_'+this.get_indice());
		    	
		    	domConstruct.create('div', {class:'row'}, this.get_dom_node());
	    		var noeud_princ = domConstruct.create('div', {
	    			id:this.dom_construct_id, 
	    			class:'notice-parent'}, this.get_dom_node());
	    		
	    		var img_plus = domConstruct.create('img', {
	    			id:this.dom_construct_id+'Img', 
	    			class:'img_plus', 
	    			hspace:'3', 
	    			border:'0', 
	    			onclick:'expandBase(\''+this.dom_construct_id+'\', true); return false;', 
	    			title:'d\351tail', 
	    			name:'imEx',
	    			src:'./images/plus.gif'
	    				}, noeud_princ);
	    		
	    		domConstruct.create('label', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_workshop_label')}, noeud_princ);
	    		var label_txt = "";
	    		if (this.workshop.get_label() != "") {
	    			label_txt += " / "+this.workshop.get_label();
	    		}
	    		this.label = domConstruct.create('span', {class:'notice-heada',innerHTML:label_txt}, noeud_princ);
	    		domConstruct.create('label', {innerHTML:' : '}, noeud_princ);
	    		if(this.workshop.get_abbreviation() == ""){
	    			this.workshop.calc_abbreviation();
	    		}
	    		this.abbreviation_node = domConstruct.create('span', {innerHTML:this.workshop.get_abbreviation()}, noeud_princ);
	    		var link_delete = domConstruct.create('a', {onclick:''}, noeud_princ);
	    		domConstruct.create('img', {src:'./images/trash.png', alt:registry.byId('nomenclature_datastore').get_message('nomenclature_js_workshop_delete'), title:registry.byId('nomenclature_datastore').get_message('nomenclature_js_workshop_delete')}, link_delete);
		    	this.own(on(link_delete, "click", lang.hitch(this, this.publish_event, 'workshop_delete')));
	    		
	    		this.instruments_list_node = domConstruct.create('div', {
	    			id:this.dom_construct_id+'Child',
	    			startOpen:"Yes",
	    			class:'notice-child',
	    			callback : "recalc_recept",
	    			style:{
	    				marginBottom:'6px',
	    				display:'none',
	    				width:'94%',
	    			}
	    		}, this.get_dom_node());
	    		
	    		domConstruct.create('label', {innerHTML:" "+registry.byId('nomenclature_datastore').get_message('nomenclature_js_workshop_name')+" : "}, this.instruments_list_node);
	    		this.input_name = domConstruct.create('input', {id:this.dom_construct_id+'_label',type:'text',value:this.workshop.get_label()}, this.instruments_list_node);
	    		this.own(on(this.input_name, 'keyup', lang.hitch(this, this.update_label_workshop)));
	    		
	    		this.init_instruments_list_ui();
		    },

		    init_instruments_list_ui: function(){
		    	var params = {
		    			id:this.workshop.instruments_list.get_hash(),
		    			instruments_list:this.workshop.instruments_list,
		    			dom_node:this.instruments_list_node,
		    			mode:"workshop"
		    	};
		    	this.instruments_list_ui = new Instruments_list_ui(params);
		    },
		    
		    set_workshop: function(workshop){
		    	this.workshop = workshop;
		    },
		    get_workshop: function(){
		    	return this.workshop;
		    },
		    set_dom_node: function(dom_node){
		    	this.dom_node = dom_node;
		    },
		    get_dom_node: function(){
		    	return this.dom_node;
		    },
		    set_indice: function(indice){
		    	this.indice = indice;
		    },
		    get_indice: function(){
		    	return this.indice;
		    },
		    set_order: function(order){
				this.workshop.set_order(order);
			},
			get_order:function(){
				return this.workshop.get_order();
			},
		    set_dom_construct_id: function(dom_construct_id){
		    	this.dom_construct_id = dom_construct_id;
		    },
		    get_dom_construct_id: function(){
		    	return this.dom_construct_id;
		    },
		    get_instruments_list_node: function() {
				return this.instruments_list_node;
			},
		    
		    maj_abbreviation: function(){
		    	this.workshop.calc_abbreviation();
		    	this.abbreviation_node.innerHTML = this.workshop.get_abbreviation();
		    },
		    update_label_workshop: function(){
				this.workshop.set_label(this.input_name.value);
		    	this.label.innerHTML = ' / '+this.workshop.get_label();
		    	this.generate_inputs();
			},
			publish_event: function(event_name){
				var event_args = {};
				event_args.hash = this.workshop.get_hash();
				switch(event_name){
					case "workshop_delete":
						event_args.order = this.workshop.get_order();
						break;
				}
				topic.publish("workshop_ui", event_name, event_args);
			},
			destroy: function(){
				for(var i=0 ; i<this.instruments_list_ui.instruments_list.instruments.length ; i++){
					this.instruments_list_ui.instruments[i].destroy();
				}
				this.instruments_list_ui.instruments_list.set_instruments(new Array());
				this.workshop = null;
				domConstruct.destroy(this.instruments_list_ui.get_dom_node());
				domConstruct.destroy(this.dom_construct_id);
				this.inherited(arguments);
			},
			generate_inputs:function(){
				for(var i=0 ; i<this.inputs_array.length ; i++){
					domConstruct.destroy(this.inputs_array[i]);
				}
				this.inputs_array.push(domConstruct.create('input',{type:'hidden', name:this.workshop.get_hidden_field_name()+'[workshops]['+this.indice+'][label]', value:this.workshop.get_label()}, this.dom_node));
				this.inputs_array.push(domConstruct.create('input',{type:'hidden', name:this.workshop.get_hidden_field_name()+'[workshops]['+this.indice+'][id]', value:this.workshop.get_id()}, this.dom_node));
				this.inputs_array.push(domConstruct.create('input',{type:'hidden', name:this.workshop.get_hidden_field_name()+'[workshops]['+this.indice+'][order]', value:this.workshop.get_order()}, this.dom_node));
			},
	    });
	});