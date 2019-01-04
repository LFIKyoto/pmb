// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Store.js,v 1.5 2018-12-28 16:27:31 tsamson Exp $


define(["dojo/_base/declare", 
        "dojo/store/Memory",
        'dojo/_base/lang',
        'dojo/topic'
], function(declare, Memory, lang, topic){
	return declare(Memory, {
		availableEntities: {},
		computedFields: [],
		environmentFields : [],
		
		constructor: function() {
			this.data.push({type: 'root'});
			this.availableEntities = new Memory({data: this.availableEntities});
		},
		
		getChildren: function(object, node) {
			if (object.type == 'property') return [];
			if (object.type == 'root') {
				var children = this.query({startScenario: 1});
				for (var key in this.environmentFields) {
					children.push({type : 'environmentFieldsType', name : this.environmentFields[key]['label'], id: key});
				}
				return children;
			}
			if (object.type == 'environmentFieldsType') {
				var children = [];
				for (var key in this.environmentFields[object.id].properties) {
					children.push({type : 'environmentField', name : this.environmentFields[object.id].properties[key], id: key, uniqueId : key});
				}
				return children;
			}
			
			var children = this.query({parent: object.id});

			if (object.type == 'form') {
				var properties = this.availableEntities.query({form_id: object.eltId, type: 'property'});
				var newProperty = {};
				properties.forEach(property => {
					newProperty = Object.assign(property);
					newProperty.uniqueId = object.id + '_' + newProperty.pmb_name;
					newProperty.alreadyComputed = false;
					if (this.computedFields.indexOf(newProperty.uniqueId) != -1) {
						newProperty.alreadyComputed = true;
					}
					children.push(newProperty);
				});
			}
			return children;
		}
	});
});
