// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_workshops_ui.js,v 1.15 2015-02-09 14:45:18 dgoron Exp $


define(["dojo/_base/declare", "apps/nomenclature/nomenclature_workshop", "apps/nomenclature/nomenclature_workshop_ui", "dojo/on", "dojo/dom-construct", "dojo/topic", "dojo/_base/lang", "dijit/registry", "dijit/_WidgetBase"], function(declare, Workshop, Workshop_ui, on, domConstruct, topic, lang, registry, _WidgetBase){
	/*
	 *Classe nomenclature_workshops_ui. Classe générant la partie du formulaire liée a un atelier
	 */
	  return declare("nomenclature_workshops_ui",[_WidgetBase], {
			
		  	nomenclature:null,
		  	workshops:null,
		  	total_workshops:0,
		  	label_total_workshops:null,
		  	dom_node:null,
		  	abbreviation_node:null,
		  	workshop_node:null,
		  	
		    constructor: function(params){
		    	this.workshops = new Array();
		    	this.own(topic.subscribe("instrument_ui", lang.hitch(this, this.handle_events)));
		    	this.own(topic.subscribe("workshop_ui", lang.hitch(this, this.handle_events)));
		    },
			
		    handle_events : function(evt_type,evt_args){
		    	switch(evt_type){
		    		case "instru_changed" :
		    			if(evt_args.hash.indexOf(this.nomenclature.get_hash()) != -1){
		    				this.maj_abbreviation();
		    			}
		    			break;
		    		case "workshop_delete" :
			    		if(evt_args.hash.indexOf(this.nomenclature.get_hash()) != -1){
			    			this.delete_workshop_event(evt_args.order);
			    			this.maj_abbreviation();
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
		    	
		    	domConstruct.create('div', {class:'row'}, this.get_dom_node());
	    		var noeud_princ = domConstruct.create('div', {
	    			id:this.get_dom_node().id+'_nomenclature_form_workshops', 
	    			class:'notice-parent'}, this.get_dom_node());
	    		
	    		var img_plus = domConstruct.create('img', {
	    			id:this.get_dom_node().id+'_nomenclature_form_workshopsImg', 
	    			class:'img_plus', 
	    			hspace:'3', 
	    			border:'0', 
	    			onclick:'expandBase(\''+this.get_dom_node().id+'_nomenclature_form_workshops\', true); return false;', 
	    			title:'d\351tail', 
	    			name:'imEx',
	    			src:'./images/plus.gif'
	    				}, noeud_princ);
	    		
	    		this.label_total_workshops = domConstruct.create('label', {innerHTML:''}, noeud_princ);
	    		domConstruct.create('label', {innerHTML:' '+registry.byId('nomenclature_datastore').get_message('nomenclature_js_workshops_label')+' : '}, noeud_princ);
	    		
	    		if(this.nomenclature.get_workshops_abbreviation() == ""){
	    			this.nomenclature.calc_workshops_abbreviation();
	    		}
	    		this.abbreviation_node = domConstruct.create('span', {innerHTML:this.nomenclature.get_workshops_abbreviation()}, noeud_princ);
	    		
	    		var link_delete = domConstruct.create('a', {onclick:''}, noeud_princ);
	    		domConstruct.create('img', {src:'./images/trash.png', alt:registry.byId('nomenclature_datastore').get_message('nomenclature_js_workshops_delete'), title:registry.byId('nomenclature_datastore').get_message('nomenclature_js_workshops_delete')}, link_delete);
		    	this.own(on(link_delete, "click", lang.hitch(this, this.delete_workshops)));
		    	
	    		this.set_workshop_node(domConstruct.create('div', {
	    			id:this.get_dom_node().id+'_nomenclature_form_workshopsChild',
	    			startOpen:"Yes",
	    			class:'notice-child',
	    			callback : "recalc_recept",
	    			style:{
	    				marginBottom:'6px',
	    				display:'none',
	    				width:'94%',
	    			}
	    		}, this.get_dom_node()));
	    		
	    		var h3_node = domConstruct.create('h3', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_workshop_add')}, this.get_workshop_node());
		    	var input_plus = domConstruct.create('input', {type:'button', value:'+', class:'bouton'}, h3_node);
		    	this.own(on(input_plus, 'click', lang.hitch(this, this.add_workshop)));
		    	this.create_part();
		    	this.total_workshops = this.workshops.length;
		    	this.label_total_workshops.innerHTML = this.total_workshops;
		    },

		    set_workshops: function(workshops){
		    	this.workshops = workshops;
		    },
		    get_workshops: function(){
		    	return this.workshops;
		    },
		    set_dom_node: function(dom_node){
		    	this.dom_node = dom_node;
		    },
		    get_dom_node: function(){
		    	return this.dom_node;
		    },
		    set_workshop_node: function(workshop_node){
		    	this.workshop_node = workshop_node;
		    },
		    get_workshop_node: function(){
		    	return this.workshop_node;
		    },
		    get_total_instruments: function() {
				return this.total_instruments;
			},
					    
			add_workshop: function(){
				var workshop = new Workshop(this.workshops.length);
				workshop.set_nomenclature(this.nomenclature);
				this.nomenclature.add_workshop(workshop);
				var params = {
						id:workshop.get_hash(),
						workshop:workshop,
						dom_node:this.workshop_node,
						indice:this.get_max_indice()+1
				};
				this.workshops.push(new Workshop_ui(params));
				this.total_workshops++;
				this.label_total_workshops.innerHTML = this.total_workshops;
				topic.publish("workshops_ui", "new_workshop",{nomenclature_hash : this.nomenclature.get_hash()});
		    },
		    
		    delete_workshop_event: function(order){
		    	var index_workshop_ui;
		    	for(var i=0 ; i<this.nomenclature.workshops.length ; i++){
		    		if(this.nomenclature.workshops[i].get_order() == order){
		    			index_workshop_ui = i; 
		    		}
		    	}
		    	this.workshops[index_workshop_ui].destroy();
		    	this.workshops.splice(index_workshop_ui, 1);
		    	this.nomenclature.delete_workshop(order, true);
		    	this.total_workshops--;
		    	this.label_total_workshops.innerHTML = this.total_workshops;
		    	topic.publish("workshops_ui", "workshop_deleted",{nomenclature_hash : this.nomenclature.get_hash()});
		    },
		    
		    create_part: function(){
		    	for(var i=0 ; i<this.nomenclature.workshops.length ; i++){
		    		var params = {
		    				id:this.nomenclature.workshops[i].get_hash(),
		    				workshop:this.nomenclature.workshops[i],
		    				dom_node:this.workshop_node,
		    				indice:i
		    		};
		    		this.workshops.push(new Workshop_ui(params));
		    	}
		    },
		    		    
		    maj_abbreviation: function(){
		    	this.nomenclature.calc_workshops_abbreviation();
		    	this.abbreviation_node.innerHTML = this.nomenclature.get_workshops_abbreviation();
		    },
		    
		    update_label_workshops: function(){
				this.workshops.set_label(this.input_name.value);
		    	this.label.innerHTML = ' / '+this.workshop.get_label();
			},
		    
			sort_nodes: function(a, b){
				if(parseInt(a.getAttribute('order')) < parseInt(b.getAttribute('order'))){
					return -1;
				}
				if(parseInt(a.getAttribute('order')) == parseInt(b.getAttribute('order'))){
					return 0;
				}
				if(parseInt(a.getAttribute('order')) > parseInt(b.getAttribute('order'))){
					return 1;
				}
			},
		    
			get_max_indice: function(){
				var max_indice=0;
		    	for(var i=0 ; i<this.workshops.length ; i++){
		    		if(this.workshops[i].get_indice()>max_indice){
		    			max_indice = this.workshops[i].get_indice();
					}
		    	}
		    	return max_indice;
		    },
			
		    generate_inputs:function(){
			    for(var i=0 ; i<this.workshops.length ; i++){
			    	for(var j=0 ; j<this.workshops[i].inputs_array.length ; j++){
						domConstruct.destroy(this.workshops[i].inputs_array[j]);
					}
			    }
				for(var i=0 ; i<this.workshops.length ; i++){
					this.workshops[i].inputs_array.push(domConstruct.create('input',{type:'hidden', name:this.nomenclature.workshops[i].get_hidden_field_name()+'[workshops]['+this.workshops[i].indice+'][label]', value:this.nomenclature.workshops[i].get_label()}, this.dom_node));
					this.workshops[i].inputs_array.push(domConstruct.create('input',{type:'hidden', name:this.nomenclature.workshops[i].get_hidden_field_name()+'[workshops]['+this.workshops[i].indice+'][id]', value:this.nomenclature.workshops[i].get_id()}, this.dom_node));
					this.workshops[i].inputs_array.push(domConstruct.create('input',{type:'hidden', name:this.nomenclature.workshops[i].get_hidden_field_name()+'[workshops]['+this.workshops[i].indice+'][order]', value:this.nomenclature.workshops[i].get_order()}, this.dom_node));
				}
			},
			
			delete_workshops: function(){
				topic.publish("workshops_ui", "workshops_delete",{nomenclature_hash : this.nomenclature.get_hash()})
			},
	    });
	});