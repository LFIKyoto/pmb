<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: extended_search_dnd.tpl.php,v 1.5 2018-10-12 13:13:51 dgoron Exp $

$extended_search_dnd_tpl = '
<link rel="stylesheet" type="text/css" href="./javascript/dojo/dojox/grid/resources/Grid.css">
<link rel="stylesheet" type="text/css" href="./javascript/dojo/dojox/grid/resources/claroGrid.css">
<style type="text/css">
div.form-contenu table.table-no-border {
	border-collapse: collapse;
}

.claro .dojoDndItemBefore,
.claro .dojoDndItemAfter {
	border-bottom: none;
	border-top: none;
}

.claro tr.dojoDndItem.dojoDndItemBefore {
	border-top: 5px solid #369;
}

.claro tr.dojoDndItem.dojoDndItemAfter {
	border-bottom: 5px solid #369;
}
</style>
<div id="!!unique_identifier!!_extended_search_dnd_container" data-dojo-type="dijit/layout/BorderContainer" data-dojo-props="splitter:true" style="height:800px;width:100%;">
</div>
<script type="text/javascript">
	require(["apps/search/!!search_controller_class!!", "dojo/domReady!"], function(SearchController){
		var searchController = new SearchController("!!unique_identifier!!_extended_search_dnd_container", "!!search_controller_module!!");
	});
</script>';