<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence.tpl.php,v 1.1 2017-07-21 08:34:40 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$explnum_licence_profile_details = "
			<h2>!!explnum_licence_label!!</h2>
			<a target='_blank' href='!!explnum_licence_uri!!'>!!explnum_licence_uri!!</a>
			
			<h3>!!explnum_licence_profile_label!!</h3>
		
			!!explnum_licence_profile_image!!<br/><br/>	
			<a target='_blank' href='!!explnum_licence_profile_uri!!'>!!explnum_licence_profile_uri!!</a>
	
			<p>!!explnum_licence_profile_explanation!!</p>
			<i>!!explnum_licence_profile_quotation_rights!!</i>
			!!explnum_licence_rights_details!!
		";


$explnum_licence_pdf_container_template = "
		<page backtop='10mm' backbottom='10mm' backleft='10mm' backright='10mm' style='text-align:center;'>
			!!explnum_licence_profiles_details!!
		</page>";

$explnum_licence_right_details = "
		!!explnum_licence_right_image!!
		<h5>!!explnum_licence_right_label!!</h5>
		<p>!!explnum_licence_right_explanation!!</p>
		";

$explnum_licence_info_picto = '<i style="cursor:pointer;" data-parsed="" class="fa fa-info-circle" data-explnum-id="!!explnum_id!!"></i>
							  <i style="cursor:pointer;" data-parsed="" class="fa fa-file-pdf-o" data-is-pdf="true" data-explnum-id="!!explnum_id!!"></i>
		';

$explnum_licence_script_dialog = '			
		<script type="text/javascript">
require(["dojo/dom", 
		"dojo/query", 
		"dojo/ready", 
		"dojo/on", 
		"dojo/request", 
		"dijit/registry", 
		"dijit/Tooltip", 
		"dojo/dom-attr", 
		"dojo/_base/lang"], function (dom, query, ready, on, request, registry, Tooltip, domAttr, lang) {
    ready(function () {
        var queryResult = query("i[data-explnum-id][data-parsed=\"\"]");
        if (queryResult.length) {
            queryResult.forEach(function (inode) {
                if (!inode.getAttribute("data-is-pdf")) {
                    on(inode, "mouseover", function (e) {
                        if (!domAttr.get(this, "id")) { 
							request.post("'.$base_path.'/ajax.php?module=ajax&categ=explnum&sub=get_licence_tooltip", {
	                            data: {
	                                id: domAttr.get(this, "data-explnum-id")
	                            },
	                            handleAs: "text"
	                        }).then(lang.hitch(this, function (data) {
	                                var date = new Date();
	                                domAttr.set(this, "id", "tooltip_explnum_"+date.getTime());
	                                var tooltip = new Tooltip({
	                                    title: "",
	                                    connectId: domAttr.get(this, "id"),
	                        			label: data,
	                                    style: {
	                                        textAlign: "center"
	                                    }
	                                });
 									tooltip.startup();
 									tooltip.open(this);
 									tooltip.close(this);
 									tooltip.open(this);
	                        }));
						}
                    });
                }else{
                    on(inode, "click", function(e){
                        window.open("'.$base_path.'/ajax.php?module=ajax&categ=explnum&sub=get_licence_as_pdf&id="+this.getAttribute("data-explnum-id"), "_blank");
                    });
                }
                domAttr.set(inode, "data-parsed", "parsed");
            });
        }
    });
});
</script>';