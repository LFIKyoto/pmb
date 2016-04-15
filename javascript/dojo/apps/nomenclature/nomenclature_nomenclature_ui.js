// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_nomenclature_ui.js,v 1.40 2015-02-10 16:33:54 dgoron Exp $

define(["dojo/_base/declare", "apps/nomenclature/nomenclature_nomenclature","apps/nomenclature/nomenclature_family_ui","apps/nomenclature/nomenclature_workshops_ui", "dojo/on", "dojo/dom-construct", "dojo/_base/lang", "dojo/dom", "dojo/topic", "dojo/dom-style", "apps/nomenclature/nomenclature_exotic_instruments_ui", "dijit/registry", "dijit/_WidgetBase"], function(declare, Nomenclature,Family_ui, Workshops_ui, on, domConstruct, lang, dom, topic, domStyle, Exotic_instruments_ui, registry, _WidgetBase){
	/*
	 *Classe nomenclature_nomenclature_ui. Classe générant la partie du formulaire liée a une nomenclature
	 */
	  return declare("nomenclature_nomenclature_ui",[_WidgetBase], {
			    
		  	/**
		  	 * 
		  	 * La classe va prendre en paramètre une instance de l'objet dojo nomenclature
		  	 * Elle va la parser, instancier les classes adéquates, et ces classes vont générer le formulaire.
		  	 */
		  	nomenclature:null,
		  	dom_node:null,
		  	families:null,
		  	input_abbrege:null,
		  	families_node:null,
		  	sync_from_abbreviation_allowed: false,
		  	sync_from_details_allowed:false,
		    total_families:0,
		    exotic_instruments_ui:null,
		    workshops_ui:null,
		    workshops_node:null,
		    total_workshops:0,
		    families_ready:false,
		    workshops_ready:false,
		    exotic_instruments_flag_ready:false,
		    main_node:null,
		    span_abbreviation:null,
		    hidden_abbr:null,
		    
		  	constructor: function(params){
		    	if(arguments[0].nomenclature_abbr){
		    		this.nomenclature = new Nomenclature(arguments[0].nomenclature_abbr,arguments[0].nomenclature_tree,arguments[0].nomenclature_indefinite_character,arguments[0].workshop_tree, arguments[0].instruments);
		    	}
		    	this.families = new Array();
		    	this.own(topic.subscribe('family_ui', lang.hitch(this, this.handle_events)));
		    	this.own(topic.subscribe('workshop_ready', lang.hitch(this, this.workshop_ready)));
		    	this.own(topic.subscribe('exotic_instruments_ready', lang.hitch(this, this.exotic_instruments_ready)));
		    	this.own(topic.subscribe("instrument_ui",lang.hitch(this, this.handle_events)));
		    	this.own(topic.subscribe("nomenclature",lang.hitch(this, this.handle_events)));
		    	this.own(topic.subscribe("workshops_ui", lang.hitch(this, this.handle_events)));
		    },
		    
		    handle_events : function(evt_type,evt_args){
		    	switch(evt_type){
		    		case "error_analyze" :
		    			if(evt_args.hash == this.nomenclature.get_hash()){
		    				this.show_analize_error(evt_args.error);
		    			}
		    		case "intru_changed" :
		    			this.allow_sync_from_details();
		    			break;
		    		case "family_ready" :
		    			if(evt_args.nomenclature_hash == this.nomenclature.get_hash()){
		    				this.family_ready();
		    			}
		    			break;
		    		case "family_changed" :
			    		if(evt_args.nomenclature_hash == this.nomenclature.get_hash()){
			    			this.allow_sync_from_details();
			    		}
			    		break;
		    		case "workshops_delete" :
			    		if(evt_args.nomenclature_hash == this.nomenclature.get_hash()){
			    			if (this.workshops_ui.workshops.length) {
			    				var workshops_indice_max = (this.workshops_ui.workshops.length)-1;
			    				for(var i=workshops_indice_max ; i>=0 ; i--){
				    				this.workshops_ui.delete_workshop_event(this.nomenclature.workshops[i].get_order());
				    			}
			    				this.workshops_ui.generate_inputs();
			    				this.workshops_ui.maj_abbreviation();
				    			this.allow_sync_from_details();
			    			}
			    		}
			    		break;
		    		case "workshops_changed" :
		    			if(evt_args.nomenclature_hash == this.nomenclature.get_hash()){
		    				if (evt_args.effective) {
		    					if (evt_args.effective > this.workshops_ui.workshops.length) {
			    					for(var i=this.workshops_ui.workshops.length; i<evt_args.effective; i++){
				    					this.workshops_ui.add_workshop();
				    				}
			    				} else if (evt_args.effective < this.workshops_ui.workshops.length) {
			    					for(var i=(this.workshops_ui.workshops.length)-1 ; i>=evt_args.effective ; i--){
					    				this.workshops_ui.delete_workshop_event(this.nomenclature.workshops[i].get_order());
					    			}
			    				}
		    				}
		    			}
			    		break;
		    	}
		    },
		    
		    show_analize_error: function(error){
		    	this.purge_instruments();
		    	domConstruct.empty(this.error_node);
		    	
		    	var abbr = "";
		    	for(var i=0 ; i<this.nomenclature.get_abbreviation().length ; i++){
		    		if(error[0].position == i){
		    			abbr+="<span style='color:red;font-weight:bold;'>"+this.nomenclature.get_abbreviation()[i]+"</span>";
		    		}else{
		    			abbr+=this.nomenclature.get_abbreviation()[i];
		    		}
		    	}
		    	domConstruct.create('div',{
		    		class:"row",
		    		innerHTML : "<div class='colonne10'><img align='left' src='./images/error.gif'></div><div class='colonne80'><b>"+registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_for_analyze')+" : </b>"+abbr+"<br>"+error[0].msg+"</div>"
		    	}, this.error_node);
		    },
		    
		    buildRendering: function(){
		    	this.inherited(arguments);
		    	this.build_form();
		    },
		    
		    build_form: function(){
		    	/***
		    	 * TODO: Create collapsable nodes
		    	 */
		    	
		    	/*******************************/
		    	//console.log('node id', this.dom_node.id);
		    	domConstruct.create('div', {class:'row'}, this.get_dom_node());
	    		var noeud_princ = domConstruct.create('div', {
	    			id:this.get_dom_node().id+'_nomenclature', 
	    			class:'notice-parent'}, this.get_dom_node());
	    		/*
	    		 * Création d'un code type "pmb" permettant de déplier les familles en cliquant sur une image
	    		 */
	    		var img_plus = domConstruct.create('img', {
	    			id:this.get_dom_node().id+'_nomenclatureImg', 
	    			class:'img_plus', 
	    			hspace:'3', 
	    			border:'0', 
	    			onclick:'expandBase(\''+this.get_dom_node().id+'_nomenclature\', true); return false;', 
	    			title:'d\351tail', 
	    			name:'imEx',
	    			src:'./images/plus.gif'
	    				}, noeud_princ);
	    		
	    		var span = domConstruct.create('span', {class:'notice-heada',innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_label')+' -'}, noeud_princ);
	    		this.span_abbreviation = domConstruct.create('span', {innerHTML:' '+this.nomenclature.get_abbreviation()}, span);
	    		//this.abbreviation_node = domConstruct.create('span', {innerHTML:' '+this.family.get_abbreviation()}, span);
	    		this.main_node = domConstruct.create('div', {
	    			id:this.get_dom_node().id+'_nomenclatureChild',
	    			startOpen:"Yes",
	    			class:'notice-child',
	    			callback : "recalc_recept",
	    			style:{
	    				marginBottom:'6px',
	    				display:'none',
	    				width:'94%',
	    			}
	    		}, this.get_dom_node());
		    	
		    	/*******************************/
		    
		    	
		    	
		    	var dom_child = domConstruct.create('div', {
		    		id:this.get_dom_node().id+"_nomenclature_control"
		    	},this.get_main_node());
		    	
		    	this.input_abbrege = domConstruct.create('input',{
		    		id:this.get_dom_node().id+'_input_abbrege',
		    		class:'saisie-80em',
		    		type:'text',
		    		value:this.nomenclature.get_abbreviation()
		    	},dom_child);
		    			    	
		    	var button_sync_details = domConstruct.create('input',{
		    		id:this.get_dom_node().id+'_button_sync_details',
		    		value:'Sync depuis abbr\351g\351',
		    		type:'button',
		    		'disabled':"disabled"
		    	}, dom_child);
		    	
		    	var button_sync_abbr = domConstruct.create('input',{
		    		id:this.get_dom_node().id+'_button_sync_abbr',
		    		value:'Sync depuis d\351tails',
		    		type:'button',
			    	'disabled':"disabled"
		    	}, dom_child);
		    	this.error_node = domConstruct.create('div', {
		    		id:this.get_dom_node().id+'_error_node'
		    	},dom_child);
		    	
		    	this.families_node = domConstruct.create('div', {
		    		id:this.get_dom_node().id+'_family_node'
		    	},dom_child);
		    	
		    	this.workshops_node = domConstruct.create('div', {
		    		id:this.get_dom_node().id+'_workshop_node'
		    	},dom_child);
		    	
		    	this.own(on(this.input_abbrege, 'keyup', lang.hitch(this, this.allow_sync_from_abbrege)));
		    	this.own(on(button_sync_details, 'click', lang.hitch(this, this.sync_from_abbrege)));
		    	this.own(on(button_sync_abbr, 'click', lang.hitch(this, this.sync_from_details)));
		    	
		    	/** Création des inputs hidden en vue de l'enregistrement d'une formation **/
		    	this.hidden_abbr = domConstruct.create('input', {type:'hidden', name:this.nomenclature.get_hidden_field_name('abbr'), value:this.nomenclature.get_abbreviation()}, dom_child);
		    	this.create_families_part();
		    	this.init_exotic_instruments();
		    	this.init_workshops();
		    },
		    
		    allow_sync_from_abbrege: function(evt){
		    	if(evt.target.value!= this.nomenclature.get_abbreviation()){
		    		this.sync_from_abbreviation_allowed = true;
		    		dom.byId(this.get_dom_node().id+'_button_sync_details').disabled=false;
		    	}else{
		    		this.sync_from_abbreviation_allowed = false;
		    		dom.byId(this.get_dom_node().id+'_button_sync_details').disabled=true;
		    	}
		    	
		    },
		    
		    allow_sync_from_details: function(){
		    	this.sync_from_details_allowed = true;
	    		dom.byId(this.get_dom_node().id+'_button_sync_abbr').disabled=false;
		    },
		    
		    sync_from_details: function(button){
		    	this.nomenclature.calc_abbreviation();
		    	var abbr = this.nomenclature.get_abbreviation();
		    	var input = dom.byId(this.get_dom_node().id+'_input_abbrege');
		    	this.sync_from_abbreviation_allowed = false;
	    		this.sync_from_details_allowed = false;
	  
	    		dom.byId(this.get_dom_node().id+'_button_sync_details').disabled=true;	
	    		dom.byId(this.get_dom_node().id+'_button_sync_abbr').disabled=true;
		    	input.value = abbr;
		    	this.maj_abbreviation();
		    },
		    sync_from_abbrege: function(){
		    	if(confirm(registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_ui_confirm_sync'))){
			    	this.purge_instruments();
			    	domConstruct.empty(this.error_node);
			    	this.sync_from_abbreviation_allowed = false;
		    		this.sync_from_details_allowed = false;
		    		dom.byId(this.get_dom_node().id+'_button_sync_details').disabled=true;	
		    		dom.byId(this.get_dom_node().id+'_button_sync_abbr').disabled=true;
			    	// le setter déclenche l'analyse 
		    		this.nomenclature.set_abbreviation(this.input_abbrege.value);
			    	this.maj_abbreviation();
		    	}
		    },
		    create_families_part: function(){
		    	for(var i=0 ; i<this.nomenclature.families.length ; i++){
		    		var params = {
		    				id:this.nomenclature.families[i].get_hash(),
		    				family:this.nomenclature.families[i],
		    				dom_node:this.families_node
		    		};
		    		this.families.push(new Family_ui(params));
		    	}
		    },
		    
		    init_workshops: function(){
		    	var params = {
		    			nomenclature:this.nomenclature, 
		    			dom_node:this.workshops_node
		    	};
	    		this.workshops_ui = new Workshops_ui(params);
		    },
		    
		    purge_instruments : function(){
		    	for(var i=0 ; i<this.families.length ; i++){
		    		this.families[i].purge_instruments();
		    	}
		    },
		    
		    set_dom_node: function(dom_node){
		    	this.dom_node = dom_node;
		    },
		    get_dom_node: function(){
		    	return this.dom_node;
		    },
		    get_nomenclature: function() {
		    	return this.nomenclature;
		    },
		    set_nomenclature: function(nomenclature) {
		    	this.nomenclature = nomenclature;
		    },
		    family_ready: function(){
		    	this.total_families++;
		    	var families = this.nomenclature.get_families();
		    	if(this.total_families == families.length){
		    		this.families_ready = true;
		    		this.check_flags();
		    	}
		    },
		    workshop_ready: function(){
		    	this.total_workshops++;
		    	var workshops = this.nomenclature.get_workshops();
		    	if(this.total_workshops == workshops.length){
		    		this.workshops_ready = true;
		    		this.check_flags();
		    	}
		    },
		    exotic_instruments_ready: function(){
		    	this.exotic_instruments_flag_ready = true;
		    	this.check_flags();
		    },
		    init_exotic_instruments: function(){
		    	var params = {
		    			instruments_list:this.nomenclature.exotic_instruments_list,
		    			dom_node:this.families_node
		    	};
		    	this.exotic_instruments_ui = new Exotic_instruments_ui(params);
		    },
		    check_flags: function(){
		    	if(this.families_ready && this.workshops_ready && this.exotic_instruments_flag_ready){
		    		init_drag();
		    		document.body.onkeypress = validation;
		    		
		    	}
		    },
		    get_main_node: function() {
				return this.main_node;
			},
			
			set_main_node: function(main_node) {
				this.main_node = main_node;
			},
			maj_abbreviation: function(){
				this.span_abbreviation.innerHTML = ' '+this.input_abbrege.value;
				this.hidden_abbr.value = this.input_abbrege.value;
			},
			
	    });
	});