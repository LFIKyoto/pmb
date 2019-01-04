// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AceManager.js,v 1.3 2017-11-22 16:23:58 dgoron Exp $

define([
     "dojo/_base/declare",
     "dojo/_base/lang",
     "dojo/dom-construct",
], function(declare, lang, domConstruct){
	return declare(null, {
	  constructor:function(){
			this.registry = {};
		  },
	  initEditor: function(id){ //Cette méthode n'est à utiliser qu'avec des textarea ou des inputs
		  var node = document.getElementById(id)
		  if(node){ //Un noeud porte l'identifiant
			  var nodeName = node.getAttribute('name');
			  var createdNode = domConstruct.create('input', {type: 'hidden', id:id, value : node.value, name:nodeName}, node, "after");
			  var editor = ace.edit(id);
			  editor.getSession().on("change", function () {
				  createdNode.setAttribute('value',editor.getSession().getValue());
		  	  });
			  
			  editor.setTheme('ace/theme/eclipse');
			  editor.getSession().setMode('ace/mode/twig');
			  editor.setOptions({
				  maxLines: Infinity,
				  minLines: 5
			  });
			  editor.getSession().setUseWorker(true);
			  this.registry[id] = editor;
		  }
	  },
	  getEditor: function(id){
		  if(this.registry){
			  if(typeof this.registry[id] != "undefined"){
				  return this.registry[id];
			  }
		  }
	  }
	});
});