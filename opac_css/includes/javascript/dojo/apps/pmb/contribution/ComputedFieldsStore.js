// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ComputedFieldsStore.js,v 1.3 2018-12-28 16:27:31 tsamson Exp $

define([
	'dojo/_base/declare',
	'dojo/store/Memory',
	'dojo/request',
	'dojo/_base/lang',
	'dojo/on',
	'dojo/query',
	'dojo/topic',
	'dojo/Deferred',
	'dojo/dom',
	'dojo/dom-attr'
], function(declare, Memory, request, lang, on, query, topic, Deferred, dom, domAttr) {
	return declare(Memory, {
		
		fieldsToModify: null,
		
		constructor: function() {
			this.deferred = new Deferred();
			request.get('ajax.php?module=ajax&categ=contribution&sub=computed_fields&area_id='+this.areaId, {
				handleAs: 'json',
				sync: true
			}).then(lang.hitch(this, function(data){
				this.data = data;
				this.initFieldsToModify();
			}));
		},
		
		initFieldsToModify: function() {
			this.fieldsToModify = [];
			for (var field of this.data) {
				for (var fieldUsed of field.fields_used) {
					if (typeof this.fieldsToModify[fieldUsed.field_num] == "undefined") {
						this.fieldsToModify[fieldUsed.field_num] = [];
					}
					this.fieldsToModify[fieldUsed.field_num].push(field.field_num);
				}
			}
		},
		
		initFormFields: function(nodeId) {
			for (var fieldNum in this.fieldsToModify) {
				query('[data-pmb-uniqueid="'+fieldNum+'"]', nodeId).forEach(lang.hitch(this, function(node) {
					var valueNode = dom.byId(node.id + '_0_value');
					on(valueNode, 'change', lang.hitch(this, function() {
						this.updateComputedFields(this.fieldsToModify[fieldNum]);
					}));
				}));
			}
			query('[data-pmb-uniqueid]', nodeId).forEach(lang.hitch(this, function(node){
				this.computeField(domAttr.get(node, 'data-pmb-uniqueid'));
			}));
		},
		
		updateComputedFields: function(fields_num) {
			for (var field_num of fields_num) {
				this.computeField(field_num);
			}
		},
		
		computeField: function(fieldNum) {
			var field = this.query({field_num: fieldNum});
			if (!field.length) {
				return false;
			}
			var fieldContent = field[0].template;
			for (fieldUsed of field[0].fields_used) {
				var parentNode = query('[data-pmb-uniqueid="'+fieldUsed.field_num+'"]');
				var value = '';
				if (parentNode.length) {
					value = dom.byId(parentNode[0].id + '_0_value').value;
				}
				if (!value && fieldUsed.value) {
					value = fieldUsed.value;
				}
				var fieldContent = fieldContent.replace('{{ ' + fieldUsed.alias + ' }}', value);
			}
			var fieldNode = query('[data-pmb-uniqueid="'+fieldNum+'"]');
			if (!fieldNode.length) {
				return false;
			}
			var fieldValueNode = dom.byId(fieldNode[0].id + '_0_value');
			fieldValueNode.value = eval(fieldContent);
		}
	});
});