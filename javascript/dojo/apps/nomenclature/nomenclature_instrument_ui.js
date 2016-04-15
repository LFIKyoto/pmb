// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_instrument_ui.js,v 1.32 2015-02-10 17:45:39 arenou Exp $


define(["dojo/_base/declare", "dojo/on","dojo/dom-construct","dojo/dom","dojo/_base/lang", "dojo/topic", "apps/nomenclature/nomenclature_instrument", "dijit/_WidgetBase", "dijit/registry", "dojo/request/xhr"], function(declare, on, domConstruct, dom, lang, topic, Instrument, _WidgetBase, registry, xhr){
	/*
	 *Classe nomenclature_instrument_ui. Classe générant la partie du formulaire liée a un instrument
	 */
	  return declare("instrument_ui",[_WidgetBase], {
			    
		  	instrument:null,
		  	dom_node:null,
		  	self_node:null,
		  	input_order:null,
		  	input_main_instr:null,
		  	input_annexe_instr:null,
		  	input_effective:null,
		  	span_order:null,
		  	indice:0,
		  	id:null,
		  	mode:null,
		  	
		    constructor: function(params, indice, dom_node, instrument, mode){
				this.set_id(params.id);
		    	this.set_instrument(instrument);
		    	this.set_dom_node(dom_node);
		    	this.set_mode(mode || "musicstand");
		    	this.set_indice(indice);
		    	this.own(topic.subscribe("instrument_ui",lang.hitch(this, this.handle_events)));
		    },
		    
		    handle_events : function(evt_type,evt_args){
		    	switch(evt_type){
		    	 	case "input_change" :
		    	 		if(evt_args.hash == this.instrument.get_hash()){
		    	 			this.input_changed();
		    	 		}
		    			break;
		    	}
		    },
		    
		    buildRendering: function(){ 
		    	this.inherited(arguments);
		    	this.domNode = domConstruct.create('tr', null, this.get_dom_node());
		    	this.domNode.setAttribute('order', this.instrument.get_order());
		    	this.domNode.setAttribute("draggable", "yes");
		    	this.domNode.setAttribute("dragtype", "instru");
		    	if (this.instrument.musicstand != null) {
		    		this.domNode.setAttribute("musicstand", this.instrument.musicstand.get_name());
		    	}
		    	this.domNode.setAttribute("id_instru", this.instrument.get_id()+'_'+this.get_indice());
		    	this.domNode.setAttribute("recept", "yes");
		    	this.domNode.setAttribute("dragtext", this.instrument.get_code());
		    	this.domNode.setAttribute("highlight", "instru_highlight");
		    	this.domNode.setAttribute("recepttype", "instru");
		    	this.domNode.setAttribute("downlight", "instru_downlight");
		    	this.domNode.setAttribute("dragicon", "./images/icone_drag_notice.png");
		    	this.domNode.setAttribute("handler", this.get_dom_node().id+'_handle_'+this.instrument.get_id()+'_'+this.get_indice());
		    	this.build_form();
		    },
		    
		    build_form: function(){
		    	var callback_change = lang.hitch(this, function(){
		    		topic.publish("instrument_ui","input_change",{
		    			hash : this.instrument.get_hash(),
		    		})
		    	});		    	
		    	var object_value = this;
	    		window.nomenclature_input_callback = function(){
	    			var id = arguments[0];
	    			if(id.match('_input_instr')){
	    				var dijit_id = id.split('_input_instr')[0];
	    			}else if(id.match('_input_other_inst')){
	    				var dijit_id = id.split('_input_other_inst')[0];
	    			}
	    			if(dijit_id != undefined){
	    				topic.publish("instrument_ui","input_change",{
			    			hash : dijit.registry.byId(dijit_id).instrument.get_hash(),
			    		})
	    			}
	    		}
	    		if((this.instrument.get_musicstand()!=undefined && !this.instrument.get_musicstand().get_divisable()) || this.mode == "exotic_instruments" || this.mode == "workshop"){
		    		var td_order = domConstruct.create('td', null, this.domNode);
			    	var span_order = domConstruct.create('span', {style:{float:'left',paddingRight:'7px'}, id:this.get_dom_node().id+'_handle_'+this.instrument.get_id()+'_'+this.get_indice()} , td_order);
			    	this.span_order = domConstruct.create('span', {style:{position:'relative',paddingRight:'7px'}, innerHTML:this.instrument.get_order(), id:this.get_dom_node().id+'_order_label_'+this.instrument.get_id()+'_'+this.get_indice()} , td_order);
			    	var img = domConstruct.create('img', {style:{width:"20px", verticalAlign:'middle'}, src:"./images/sort.png"}, span_order);
	
			    	var td_main_instr = domConstruct.create('td', null,this.domNode);
			    	this.input_main_instr = domConstruct.create('input', {
			    		name:this.get_id()+'_input_instr', 
			    		type:'text', 
			    		id:this.get_id()+'_input_instr', 
			    		value:this.instrument.get_code(),
			    		autocomplete:'off',
			    		completion:'instruments',
			    		callback:"nomenclature_input_callback",
			    		autfield:this.get_id()+'_input_instr'
			    	},td_main_instr);
			    	this.own(on(this.input_main_instr, 'change', callback_change));
			    	
			    	
			    	switch (this.mode) {
				    	case "exotic_instruments":
				    		var others_inst = ""; 
					    	if(this.instrument.others_instruments!=null){
					    		var others_inst_array = this.instrument.get_others_instruments();
					    		for(var i=0 ; i<others_inst_array.length ; i++){
					    			others_inst+=others_inst_array[i].get_code();
					    			if(i<others_inst_array.length-1){
					    				others_inst+='/';
					    			}
					    		}
					    	}
					    	
					    	var td_annexe_instr = domConstruct.create('td', null,this.domNode);
					    	this.input_annexe_instr = domConstruct.create('input', {
					    		name:this.get_id()+'_input_other_inst', 
					    		id:this.get_id()+'_input_other_inst', 
					    		type:'text', 
					    		value:others_inst,
					    		autocomplete:'off',
					    		completion:'instruments',
					    		callback:"nomenclature_input_callback",
					    		separator:'/',
					    		autfield:this.get_id()+'_input_other_inst'
					    	}, td_annexe_instr);
					    	this.own(on(this.input_annexe_instr, 'change', callback_change));
					    	var td_effective = domConstruct.create('td', null,this.domNode);
					    	this.input_effective = domConstruct.create('input', {
					    		name:this.get_id()+'_input_effective', 
					    		id:this.get_id()+'_input_effective', 
					    		type:'text', 
					    		value:this.instrument.get_effective(),
					    	}, td_effective);
					    	this.own(on(this.input_effective, 'change', callback_change));
					    	break;
				    	case "workshop":
				    		var td_effective = domConstruct.create('td', null,this.domNode);
					    	this.input_effective = domConstruct.create('input', {
					    		name:this.get_id()+'_input_effective', 
					    		id:this.get_id()+'_input_effective', 
					    		type:'text', 
					    		value:this.instrument.get_effective(),
					    	}, td_effective);
					    	this.own(on(this.input_effective, 'change', callback_change));
					    	break;
				    	case "musicstand":
				    		var others_inst = ""; 
					    	if(this.instrument.others_instruments!=null){
					    		var others_inst_array = this.instrument.get_others_instruments();
					    		for(var i=0 ; i<others_inst_array.length ; i++){
					    			others_inst+=others_inst_array[i].get_code();
					    			if(i<others_inst_array.length-1){
					    				others_inst+='/';
					    			}
					    		}
					    	}
					    	var td_annexe_instr = domConstruct.create('td', null,this.domNode);
					    	this.input_annexe_instr = domConstruct.create('input', {
					    		name:this.get_id()+'_input_other_inst', 
					    		id:this.get_id()+'_input_other_inst', 
					    		type:'text', 
					    		value:others_inst,
					    		autocomplete:'off',
					    		completion:'instruments',
					    		callback:"nomenclature_input_callback",
					    		separator:'/',
					    		autfield:this.get_id()+'_input_other_inst'
					    	}, td_annexe_instr);
					    	this.own(on(this.input_annexe_instr, 'change', callback_change));
					    	break;
			    	}
	    		}else{
		    		var td_order = domConstruct.create('td', null, this.domNode);
			    	var span_order = domConstruct.create('span', {style:{float:'left',paddingRight:'7px'}, id:this.get_dom_node().id+'_handle_'+this.instrument.get_id()+'_'+this.get_indice()} , td_order);
			    	this.span_order = domConstruct.create('span', {style:{position:'relative',paddingRight:'7px'}, innerHTML:this.instrument.get_order(), id:this.get_dom_node().id+'_part_label_'+this.instrument.get_id()+'_'+this.get_indice()} , td_order);
			    	var img = domConstruct.create('img', {style:{width:"20px", verticalAlign:'middle'}, src:"./images/sort.png"}, span_order);
			    	
			    	var td_effective = domConstruct.create('td', null,this.domNode);
			    	this.input_effective = domConstruct.create('input', {
			    		name:this.get_id()+'_input_effective', 
			    		id:this.get_id()+'_input_effective', 
			    		type:'text', 
			    		value:this.instrument.get_effective(),
			    	}, td_effective);
			    	this.own(on(this.input_effective, 'change', callback_change));
			    	
			    	var td_main_instr = domConstruct.create('td', null,this.domNode);
			    	this.input_main_instr = domConstruct.create('input', {
			    		name:this.get_id()+'_input_instr', 
			    		type:'text', 
			    		id:this.get_id()+'_input_instr', 
			    		value:this.instrument.get_code(),
			    		autocomplete:'off',
			    		completion:'instruments',
			    		callback:"nomenclature_input_callback",
			    		autfield:this.get_id()+'_input_instr'
			    	},td_main_instr);
			    	this.own(on(this.input_main_instr, 'change', callback_change));
	    		}
		    	var td_suppression = domConstruct.create('td', null, this.domNode);
		    	var bouton_delete = domConstruct.create('input', {
		    		type:'button', 
		    		value:'X',
		    		class:" bouton"
		    	}, td_suppression);
		    	
		    	this.own(on(bouton_delete, "click", lang.hitch(this, this.publish_event, 'instrument_delete')));
		    	this.init_actions(td_suppression);
		    	this.ajax_parse();
		    },
		    
		    init_actions: function (parent_node){
		    	switch(this.mode){
			    	case "musicstand": 
			    		if(this.instrument.musicstand.family.nomenclature.record_formation.get_record() != 0){
				    		//TODO check
				    		var args = "&record_child_data[num_formation]="+this.instrument.musicstand.family.nomenclature.record_formation.formation.get_id();
				    		args+="&record_child_data[num_musicstand]="+this.instrument.musicstand.get_id();
				    		args+="&record_child_data[num_instrument]="+this.instrument.get_id();
				    		args+="&record_child_data[num_voice]=0";
				    		args+="&record_child_data[other]="+this.instrument.get_others_instruments();
				    		args+="&record_child_data[effective]="+this.instrument.get_effective();
				    		args+="&record_child_data[order]="+this.instrument.get_order();
				    		xhr("./ajax.php?module=ajax&categ=nomenclature&sub=record_child&action=get_child&id_parent="+this.instrument.musicstand.family.nomenclature.record_formation.get_record(), {
								handleAs: "json",
								method:"POST",
								data:args
							}).then(lang.hitch(this,this.got_record,parent_node),function(err){console.log(err)})
				    	}
			    		break;
		    	}
		    	
		    },
		    
		    got_record : function(parent_node,record_id) {
		    	if(record_id!= 0){
		    		var show = domConstruct.create("input",{
		    			type: "button",
		    			class: "bouton",
		    			value: registry.byId("nomenclature_datastore").get_message("nomenclature_js_see_record")
		    		},parent_node);
		    		this.own(on(show,"click",function(){
		    			window.open("./catalog.php?categ=modif&id="+record_id);
		    		}));
		    		
		    	}else{
		    		var create = domConstruct.create("input",{
		    			type: "button",
		    			class: "bouton",
		    			value: registry.byId("nomenclature_datastore").get_message("nomenclature_js_create_record")
		    		},parent_node);
		    		this.own(on(create,"click",lang.hitch(this,this.create_record,create)));
		    	}
		    },
		    
		    create_record: function(parent){
		    	var args = "&record_child_data[num_formation]="+this.instrument.musicstand.family.nomenclature.record_formation.formation.get_id();
	    		args+="&record_child_data[num_musicstand]="+this.instrument.musicstand.get_id();
	    		args+="&record_child_data[num_instrument]="+this.instrument.get_id();
	    		args+="&record_child_data[num_voice]=0";
	    		args+="&record_child_data[other]="+this.instrument.get_others_instruments();
	    		args+="&record_child_data[effective]="+this.instrument.get_effective();
	    		args+="&record_child_data[order]="+this.instrument.get_order();
	    		xhr("./ajax.php?module=ajax&categ=nomenclature&sub=record_child&action=create&id_parent="+this.instrument.musicstand.family.nomenclature.record_formation.get_record(), {
					handleAs: "json",
					method:"POST",
					data:args
				}).then(lang.hitch(this,this.record_created,parent),function(err){console.log(err)})
		    },
		    
		    record_created: function(button,record){
		    	if(record){
		    		var show = domConstruct.create("input",{
		    			type: "button",
		    			class: "bouton",
		    			value: registry.byId("nomenclature_datastore").get_message("nomenclature_js_see_record")
		    		},button.parentNode);
		    		button.parentNode.removeChild(button)
		    		this.own(on(show,"click",function(){
		    			window.open("./catalog.php?categ=modif&id="+record.id);
		    		}));
		    		topic.publish('instrument_ui',"partial_record_created",{
		    			hash: this.instrument.get_hash(),
		    			record_id: record.id,
		    			record_title: record.title
		    		});
		    	}
		    },
		    
		    reorder: function(){
		    	this.input_order.value = this.instrument.get_order();
		    },
		    input_change_order: function(){
		    	if(!isNaN(this.input_order.value)){
		    		this.instrument.set_order(parseInt(this.input_order.value));	
		    	}
		    },
		    input_changed: function(instrument_changed){
	    		if(this.input_main_instr.value != ""){
	    			this.instrument.set_code(this.input_main_instr.value.trim());
	    			var tree_instruments_datastore = registry.byId('nomenclature_datastore').get_instruments_datastore();
	    			for(var i=0 ; i<tree_instruments_datastore.length ; i++){
	    				if (tree_instruments_datastore[i]['code'] == this.instrument.get_code()) {
	    					this.instrument.set_name(tree_instruments_datastore[i]['name']);
	    					break;
	    				}
					}
	    			if (this.instrument.musicstand != null) {
		    			if(typeof this.instrument.musicstand.get_standard_instrument == "function"){
		    				if(this.input_main_instr.value.trim() != this.instrument.musicstand.get_standard_instrument().get_code()){
		    					this.instrument.set_standard(false);
		    				}else{
		    					this.instrument.set_standard(true);
		    				}
	    				}
	    			}
	    		}
	    		if (this.input_annexe_instr) {
	    			var new_values_other_inst = this.input_annexe_instr.value;
			    	var new_values_other_inst = new_values_other_inst.split('/');
			    	
			    	this.instrument.others_instruments = null;
			    	
			    	for(var i=0 ; i<new_values_other_inst.length ; i++){
			    		new_values_other_inst[i] = new_values_other_inst[i].trim(); 
			    		if(new_values_other_inst[i].length>0){
			    			var new_other_inst = new Instrument(new_values_other_inst[i])
			    			if(this.instrument.others_instruments == null){
			    				this.instrument.others_instruments = new Array();
			    			}
			    			this.instrument.add_other_instrument(new_other_inst);
			    		}
			    	}
	    		}
		    	if(this.input_effective != null){
		    		this.instrument.set_effective(parseInt(this.input_effective.value));
		    		/**
		    		 * TODO: Update effective(means event & recalc sur instrument list);
		    		 */
		    	}
		    	
		    	this.publish_event("instru_changed");
		    	
		    },
		    get_dom_node: function() {
				return this.dom_node;
			},
			set_dom_node: function(dom_node) {
				this.dom_node = dom_node;
			},
			get_mode: function() {
				return this.mode;
			},
			set_mode: function(mode) {
				this.mode = mode;
			},
			get_instrument: function() {
				return this.instrument;
			},
			set_instrument: function(instrument) {
				this.instrument = instrument;
			},
			get_indice: function() {
				return this.indice;
			},
			
			set_indice: function(indice) {
				this.indice = indice;
			},
			set_order: function(order){
				this.instrument.set_order(order);
				this.domNode.setAttribute("order", order);
				this.span_order.innerHTML = order;
				this.publish_event('instru_changed');
			},
			get_order:function(){
				return this.instrument.get_order();
			},
			get_part: function() {
				return this.instrument.get_part();
			},
			
			postCreate:function(){
				this.inherited(arguments);
				parse_drag(this.dom_node);

				this.publish_event("dom_ready");
			},
			destroy: function(){
				this.instrument = null;
				this.inherited(arguments);
			},
			set_id: function(id){
				this.id = id;
			},
			get_id: function(){
				return this.id;
			},
			ajax_parse: function(){
				if (this.input_main_instr) ajax_pack_element(this.input_main_instr);
				if (this.input_annexe_instr) ajax_pack_element(this.input_annexe_instr);
			},
			publish_event: function(event_name){
				var event_args = {};
				event_args.mode = this.mode;
				event_args.hash = this.instrument.get_hash();
				switch(this.mode){
					case "musicstand":
						event_args.musicstand_hash = this.instrument.musicstand.get_hash();
						break;
					case "workshop" :
						//event_args.musicstand_hash = this.instrument.musicstand.get_hash();
						break;
					case "exotic_instruments":
						//event_args.musicstand_hash = this.instrument.musicstand.get_hash();
						break;
				}
				switch(event_name){
					case "instrument_delete":
							event_args.order = this.instrument.get_order();	
						break;
				}
				/**
				 * For debug
				 * console.log(' publish evt: ', event_name, 'evt args;', event_args);
				 */
				topic.publish("instrument_ui", event_name, event_args);
			},
	    });
	});