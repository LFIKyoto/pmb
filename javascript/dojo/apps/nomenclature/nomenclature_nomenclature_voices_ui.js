// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_nomenclature_voices_ui.js,v 1.2 2015-02-09 11:15:03 vtouchard Exp $

define(["dojo/_base/declare", "dojo/dom-construct", "dojo/topic", "apps/nomenclature/nomenclature_voices_list_ui", "dojo/on", "dojo/_base/lang", "dijit/_WidgetBase", "apps/nomenclature/nomenclature_nomenclature_voices", "dijit/registry", "dojo/dom"], function(declare, domConstruct, topic, Voices_list_ui,on,lang, _WidgetBase, Nomenclature_voices, registry, dom){
	/*
	 *Classe nomenclature_nomenclature_voices_ui. Classe gérant l'affichage d'une nomenclature de type voix
	 */
	  return declare("nomenclature_nomenclature_voices_ui", [_WidgetBase], {
		
		  	nomenclature_voices:null, /** Instance du modele lié **/
		  	record_formation_ui:null, /** Instance de l'ui parent **/
		  	voices_list_ui:null,	  /** Instance de l'ui gérée par cette ui **/
		  	id:0,
		  	events_handles: null,
		  	dom_node:null,
		  	span_abbreviation:null,
		    hidden_abbr:null,
		  	sync_from_abbreviation_allowed: false,
		  	sync_from_details_allowed:false,
		  	voices_list_node:null,
		    
		    constructor: function(params, dom_node){
		    	this.events_handles = new Array();
		    	this.set_nomenclature_voices(params.nomenclature_voices);
		    	this.set_dom_node(dom_node);
		    	
		    	this.set_record_formation_ui(params.record_formation_ui);
		    	this.events_handles.push(topic.subscribe('voices_list_ui', lang.hitch(this, this.handle_events)));
		    	this.events_handles.push(topic.subscribe('nomenclature_voices', lang.hitch(this, this.handle_events)));
		    },
		    
		    buildRendering: function(){ 
		    	this.inherited(arguments);
		    	this.build_form();
		    },
		    
		    handle_events: function(evt_type, evt_args){
		    	switch(evt_type){
		    		case 'voices_list_changed':
		    			if(evt_args.hash.indexOf(this.nomenclature_voices.get_hash()) != -1){
		    				this.allow_sync_from_details()
		    	 		}
		    			break;
		    		case 'voices_list_reordered':
		    			if(evt_args.hash.indexOf(this.nomenclature_voices.get_hash()) != -1){
		    				this.update_after_reord();
		    	 		}
		    			break;
		    		case 'error_analyze':
		    			if(evt_args.hash.indexOf(this.nomenclature_voices.get_hash()) != -1){
		    				this.show_analize_error(evt_args.error);
		    	 		}
		    			break;
		    	}
		    },
		    
		    build_form: function(){
		    	
		    	domConstruct.create('div', {class:'row'}, this.get_dom_node());
	    		/** Création du noeud extensible **/
		    	var noeud_princ = domConstruct.create('div', {
	    			id:this.get_dom_node().id+'_nomenclature_voices', 
	    			class:'notice-parent'}, this.get_dom_node());

	    		var img_plus = domConstruct.create('img', {
	    			id:this.get_dom_node().id+'_nomenclature_voicesImg', 
	    			class:'img_plus', 
	    			hspace:'3', 
	    			border:'0', 
	    			onclick:'expandBase(\''+this.get_dom_node().id+'_nomenclature_voices\', true); return false;', 
	    			title:'d\351tail', 
	    			name:'imEx',
	    			src:'./images/plus.gif'
	    				}, noeud_princ);
	    		var span = domConstruct.create('span', {class:'notice-heada',innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_voices_label')+' '}, noeud_princ);
	    		this.span_abbreviation = domConstruct.create('span', null, span);
	    		if(this.nomenclature_voices.get_abbreviation() && this.nomenclature_voices.get_abbreviation()!="")
	    			this.span_abbreviation.innerHTML='- '+this.nomenclature_voices.get_abbreviation()
	    		
	    		
	    		/** Création du noeud enfant, affiché lors de l'appui sur le bouton plus **/
	    		var noeuf_enfant = domConstruct.create('div', {
	    			id:this.get_dom_node().id+'_nomenclature_voicesChild',
	    			startOpen:"Yes",
	    			class:'notice-child',
	    			callback : "recalc_recept",
	    			style:{
	    				marginBottom:'6px',
	    				display:'none',
	    				width:'94%',
	    			}
	    		}, this.get_dom_node());
	    		
		    	var dom_child = domConstruct.create('div', {
		    		id:this.get_dom_node().id+"_nomenclature_control_voices"
		    	},noeuf_enfant);
		    	
		    	this.input_abbrege = domConstruct.create('input',{
		    		id:this.get_dom_node().id+'_input_abbrege_voices',
		    		class:'saisie-80em',
		    		type:'text',
		    		value:this.nomenclature_voices.get_abbreviation()
		    	},dom_child);
		    			    	
		    	var button_sync_details = domConstruct.create('input',{
		    		id:this.get_dom_node().id+'_button_sync_details_voices',
		    		value:'Sync depuis abbr\351g\351',
		    		type:'button',
		    		'disabled':"disabled"
		    	}, dom_child);
		    	
		    	var button_sync_abbr = domConstruct.create('input',{
		    		id:this.get_dom_node().id+'_button_sync_abbr_voices',
		    		value:'Sync depuis d\351tails',
		    		type:'button',
			    	'disabled':"disabled"
		    	}, dom_child);
		    	
		    	this.error_node = domConstruct.create('div', {
		    		id:this.get_dom_node().id+'_error_node_voices'
		    	},dom_child);
		    	
		    	this.voices_list_node = domConstruct.create('div', {
		    		id:this.get_dom_node().id+'_voices_list_node'
		    	},dom_child);

		    	on(this.input_abbrege, 'keyup', lang.hitch(this, this.allow_sync_from_abbrege));
		    	on(button_sync_details, 'click', lang.hitch(this, this.sync_from_abbrege));
		    	on(button_sync_abbr, 'click', lang.hitch(this, this.sync_from_details));
		    	
		    	/** Création des inputs hidden en vue de l'enregistrement d'une formation **/
		    	this.hidden_abbr = domConstruct.create('input', {type:'hidden', name:this.nomenclature_voices.get_hidden_field_name('abbr'), value:this.nomenclature_voices.get_abbreviation()}, dom_child);
		    	this.init_voices_list_ui();
		    },
		    
		    init_voices_list_ui:function(){
		    	var obj = {voices_list:this.nomenclature_voices.voices_list,nomenclature_voices_ui:this};
		    	this.voices_list_ui = new Voices_list_ui(obj, this.get_voices_list_node());
		    },
		    
		    get_id: function() {
				return this.id;
			},
			
			set_id: function(id) {
				this.id = id;
			},
			
			get_voices_list: function() {
				return this.voices_list;
			},
			
			set_voices_list: function(voices_list) {
				this.voices_list = voices_list;
			},
			
			get_record_formation_ui: function() {
				return this.record_formation_ui;
			},
			
			set_record_formation_ui: function(record_formation_ui) {
				this.record_formation_ui = record_formation_ui;
			},
			
			get_nomenclature_voices: function() {
				return this.nomenclature_voices;
			},
			
			set_nomenclature_voices: function(nomenclature_voices) {
				this.nomenclature_voices = nomenclature_voices;
			},
			
			get_voices_list_ui: function() {
				return this.voices_list_ui;
			},
			
			set_voices_list_ui: function(voices_list_ui) {
				this.voices_list_ui = voices_list_ui;
			},
			
			destroy: function(){
				for(var i=0 ; i<this.events_handles.length ; i++){
					this.events_handles[i].remove();
				}
				this.nomenclature_voices = null;
				this.inherited(arguments);
			},
			
			get_dom_node: function() {
				return this.dom_node;
			},
			
			set_dom_node: function(dom_node) {
				this.dom_node = dom_node;
			},
			
			get_error_node: function() {
				return this.error_node;
			},
			
			set_error_node: function(error_node) {
				this.error_node = error_node;
			},
			
			get_voices_list_node: function() {
				return this.voices_list_node;
			},
			
			set_voices_list_node: function(voices_list_node) {
				this.voices_list_node = voices_list_node;
			},
			
		    allow_sync_from_abbrege: function(evt){
		    	if(evt.target.value!= this.nomenclature_voices.get_abbreviation()){
		    		this.sync_from_abbreviation_allowed = true;
		    		dom.byId(this.get_dom_node().id+'_button_sync_details_voices').disabled=false;
		    	}else{
		    		this.sync_from_abbreviation_allowed = false;
		    		dom.byId(this.get_dom_node().id+'_button_sync_details_voices').disabled=true;
		    	}
		    	
		    },
			
			allow_sync_from_details: function(){
			    	this.sync_from_details_allowed = true;
					dom.byId(this.get_dom_node().id+'_button_sync_abbr_voices').disabled=false;
			},
			
			sync_from_details: function(button){
				domConstruct.empty(this.error_node);
				this.nomenclature_voices.calc_abbreviation();
				var abbr = this.nomenclature_voices.get_abbreviation();
				var input = dom.byId(this.get_dom_node().id+'_input_abbrege_voices');
		    	this.sync_from_abbreviation_allowed = false;
	    		this.sync_from_details_allowed = false;
	  
	    		dom.byId(this.get_dom_node().id+'_button_sync_details_voices').disabled=true;	
				dom.byId(this.get_dom_node().id+'_button_sync_abbr_voices').disabled=true;
				input.value = abbr;
				this.maj_abbreviation();
			},
			sync_from_abbrege: function(){
				this.purge_voices();
				domConstruct.empty(this.error_node);
				this.sync_from_abbreviation_allowed = false;
				this.sync_from_details_allowed = false;
				dom.byId(this.get_dom_node().id+'_button_sync_details_voices').disabled=true;	
				dom.byId(this.get_dom_node().id+'_button_sync_abbr_voices').disabled=true;
				this.nomenclature_voices.set_abbreviation(this.input_abbrege.value);
				this.nomenclature_voices.analyze();
				this.maj_abbreviation();
				/** TODO: Update List **/
			},
		    
			maj_abbreviation: function(){
				this.span_abbreviation.innerHTML = '- '+this.input_abbrege.value;
				this.hidden_abbr.value = this.input_abbrege.value;
			},
		    show_analize_error: function(error){
		    	this.purge_voices();
		    	domConstruct.empty(this.error_node);
		    	var abbr = "";
		    	for(var i=0 ; i<this.nomenclature_voices.get_abbreviation().length ; i++){
		    		if(error[0].position == i){
		    			abbr+="<span style='color:red;font-weight:bold;'>"+this.nomenclature_voices.get_abbreviation()[i]+"</span>";
		    		}else{
		    			abbr+=this.nomenclature_voices.get_abbreviation()[i];
		    		}
		    	}
		    	domConstruct.create('div',{
		    		class:"row",
		    		innerHTML : "<div class='colonne10'><img align='left' src='./images/error.gif'></div><div class='colonne80'><b>"+registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_for_analyze')+" : </b>"+abbr+"<br>"+error[0].msg+"</div>"
		    	}, this.error_node);
		    	this.hidden_abbr.value = "";
		    	topic.publish('nomenclature_voices_ui', "hide_node", {hash:this.nomenclature_voices.get_hash()});
		    },
		    purge_voices : function(){
		    	this.voices_list_ui.purge_voices();
		    },
		    update_after_reord: function(){
		    	this.input_abbrege.value = this.nomenclature_voices.voices_list.get_abbreviation();
		    	this.hidden_abbr.value = this.input_abbrege.value;
				this.sync_from_abbreviation_allowed = false;
				this.sync_from_details_allowed = false;
				dom.byId(this.get_dom_node().id+'_button_sync_details_voices').disabled=true;	
				dom.byId(this.get_dom_node().id+'_button_sync_abbr_voices').disabled=true;
		    },
	    });
	});