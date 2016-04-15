// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax.js,v 1.33 2015-02-16 13:51:22 jpermanne Exp $

requete=new Array();
line=new Array();
not_show=new Array();
last_word=new Array();
ids=new Array();
ajax_listener = new Array();

var position_curseur;

function isFirefox1() {
	if(navigator.userAgent.indexOf("Firefox")!=-1){
		var versionindex=navigator.userAgent.indexOf("Firefox")+8
		if (parseInt(navigator.userAgent.substr(versionindex))>1) {
			if (parseInt(navigator.userAgent.substr(versionindex))==2) {
				if (navigator.userAgent.substr(versionindex,7)=="2.0.0.2") 
					return false;
				else
					return true;
			} else return true;
		} else return true;
	} else return true;
}

function findPos(obj) {
	var curleft = curtop = 0
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
				curleft += obj.offsetLeft;
				curtop += obj.offsetTop;
		}
	}
	return [curleft,curtop];
}

function setCursorPosition(ctrl, pos){
	
	
	if(ctrl.setSelectionRange){
		ctrl.focus();
		ctrl.setSelectionRange(pos,pos);
	} else if (ctrl.createTextRange) {
		var range = ctrl.createTextRange();
		range.collapse(true);
		range.moveEnd('character', pos);
		range.moveStart('character', pos);
		range.select();
	}
}

function ajax_resize_element(input){
	var id="";
	n=ids.length;
	if (input.getAttribute("completion")) {
		if (((input.getAttribute("type")=="text")||(input.nodeName=="TEXTAREA"))&&(input.getAttribute("id"))) {
			ids[n]=input.getAttribute("id");		
			id=ids[n];
			w=input.clientWidth
			d1= document.getElementById("d"+id);
			d1.style.width=w+"px";
		}
	}
}

function ajax_resize_elements(){
	var inputs=document.getElementsByTagName("input");
	for (i=0; i<inputs.length; i++) {
		ajax_resize_element(inputs[i]);
	}
	var textareas=document.getElementsByTagName("textarea");
	for (i=0; i<textareas.length; i++) {
		ajax_resize_element(textareas[i]);
	}
}

function ajax_pack_element(inputs) {
	var id="";
	n=ids.length;
	var touche=inputs.getAttribute("keys");
	if(!touche || touche==''){
		touche='40,113';
	}
	if (inputs.getAttribute("completion")) {
		if (((inputs.getAttribute("type")=="text")||(inputs.nodeName=="TEXTAREA"))&&(inputs.getAttribute("id"))) {
			ids[n]=inputs.getAttribute("id");		
			id=ids[n];
			//Insertion d'un div parent
			w=inputs.clientWidth;
			d=document.createElement("span");
			d.style.width=w+"px";
			p=inputs.parentNode;
			var input=inputs;
			p.replaceChild(d,inputs);
			d.appendChild(input);
			d1=document.createElement("div");
			d1.setAttribute("id","d"+id);
			d1.style.width=w+"px";
			d1.style.border="1px #000 solid";
			//poss=findPos(input);
			d1.style.left="0px";
			d1.style.top="0px";
			d1.style.display="none";
			d1.style.position="absolute";
			d1.style.backgroundColor="#FFFFFF";
			d1.style.zIndex=1000;
			document.getElementById('att').appendChild(d1);
			if (input.addEventListener) {	
				input.addEventListener("keyup",function(e) { ajax_update_info(e,'up',touche); },false);
				input.addEventListener("blur",function(e) { ajax_hide_list(e); },false);
			} else if (input.attachEvent) {
				input.attachEvent("onkeydown",function() { ajax_update_info(window.event,'down',touche); });//Pour internet explorer il faut que je capte l'appuie sur "entrée" avant le formulaire
				input.attachEvent("onkeyup",function() { ajax_update_info(window.event,'up',touche); });
				input.attachEvent("onblur",function() { ajax_hide_list(window.event); });
			}
			//on retire l'autocomplete du navigateur...
			input.setAttribute("autocomplete","off");
		}
	} 
	requete[id]="";
	line[id]=0;
	not_show[id]=true;
	last_word[id]="";	
}

function ajax_parse_dom() {
	var inputs=document.getElementsByTagName("input");
	for (i=0; i<inputs.length; i++) {
		ajax_pack_element(inputs[i]);
	}
	var textareas=document.getElementsByTagName("textarea");
	for (i=0; i<textareas.length; i++) {
		ajax_pack_element(textareas[i]);
	}
	
	document.body.onkeypress = validation;
}

function ajax_hide_list(e) {
	if (e.target) var id=e.target.getAttribute("id"); else var id=e.srcElement.getAttribute("id");
	setTimeout("document.getElementById('d"+id+"').style.display='none'; not_show['"+id+"']=true;",500);
}		

function ajax_set_datas(sp_name,id,insert_between_separator) {
	var sp=document.getElementById(sp_name);
	var nom_div = sp_name.substr(1,sp_name.length);
	var position_curseur = document.getElementById(id).selectionStart;
	if(sp_name.charAt(0) == 'c'){
		var nom_div = sp_name.substr(1,sp_name.length);
		nom_div='l'+nom_div;
		var div_txt=document.getElementById(nom_div);
		var taille_txt = div_txt.firstChild.nodeValue.length;
		var taille_search = sp.getAttribute('nbcar');
	}
	var text=sp.firstChild.nodeValue;
	var old_text = document.getElementById(id).value;
	if (insert_between_separator != '') {
		if ( typeof document.getElementById(id).selectionStart != 'undefined' ) {
			var sep_end = old_text.length;
			var tmp_text = old_text;
			if (old_text.indexOf(insert_between_separator,document.getElementById(id).selectionStart) != '-1') {
				sep_end = old_text.indexOf(insert_between_separator,document.getElementById(id).selectionStart);
				tmp_text = old_text.substr(0,sep_end);
			}
			var sep_start = 0;
			if (tmp_text.lastIndexOf(insert_between_separator,document.getElementById(id).selectionStart) != '-1') {
				sep_start = tmp_text.lastIndexOf(insert_between_separator,document.getElementById(id).selectionStart)+1;
			}
			taille_search = position_curseur - sep_start;
			var taille_txt = sp.firstChild.nodeValue.length;
			text = old_text.substr(0,sep_start)+text+old_text.substr(sep_end);
		}
	}
	var autfield=document.getElementById(id).getAttribute("autfield");
	if (autfield && document.getElementById(nom_div)) {
		var autid=document.getElementById(nom_div).getAttribute("autid");
		document.getElementById(autfield).value=autid;
		var thesid = document.getElementById(nom_div).getAttribute("thesid");
		if(thesid && thesid >0){
			var theselector = document.getElementById(autfield.replace('field','fieldvar').replace("_id","")+"[id_thesaurus][]");
			if(theselector){
				for (var i=1 ; i< theselector.options.length ; i++){
					if (theselector.options[i].value == thesid){
						theselector.options[i].selected = true;
						break;
					}
				}
			}
		}
		var type = document.getElementById(nom_div).getAttribute("typeuri");
		if (type && (autfield.indexOf('value', 0) != -1)) document.getElementById(autfield.replace('value','type')).value = type;
	} else if(autfield){
		document.getElementById(autfield).value=sp.getAttribute("autid");
		var thesid = sp.getAttribute("thesid");
		if(thesid && thesid >0){
			var theselector = document.getElementById(autfield.replace('field','fieldvar').replace("_id","")+"[id_thesaurus][]");
			if(theselector){
				for (var i=1 ; i< theselector.options.length ; i++){
					if (theselector.options[i].value == thesid){
						theselector.options[i].selected = true;
						break;
					}
				}
			}
		}
		var type = sp.getAttribute("typeuri");
		if (type && (autfield.indexOf('value', 0) != -1)) document.getElementById(autfield.replace('value','type')).value = type;
	}
	
	var callback=document.getElementById(id).getAttribute("callback");
	document.getElementById(id).value=text;
	document.getElementById(id).focus();
	document.getElementById("d"+id).style.display='none';
	not_show[id]=true;
	if(taille_txt) setCursorPosition(document.getElementById(id), (position_curseur+taille_txt)-taille_search);
	if (callback) window[callback](id);
}
		
function ajax_update_info(e,code,touche) {
	if (e.target) var id=e.target.getAttribute("id"); else var id=e.srcElement.getAttribute("id");
	
	if((code == "down") && (e.keyCode != 13)){
		return;
	}
	
	switch (e.keyCode) {
		case 27:	//Echap
			if (document.getElementById("d"+id).style.display=="block") {
				document.getElementById("d"+id).style.display='none';
				not_show[id]=true;
				e.cancelBubble = true;
				if (e.stopPropagation) { e.stopPropagation(); }
			}
			break;
		case 40:	//Flèche bas
			next_line=line[id]+1;
			if (document.getElementById("d"+id).style.display=="block") {
				if (document.getElementById("l"+id+"_"+next_line)==null) break;
				old_line=line[id];
				line[id]++;
				sp=document.getElementById("l"+id+"_"+line[id]);
				sp.style.background='#000088';
				sp.style.color='#FFFFFF';
				if (old_line) {
					sp_old=document.getElementById("l"+id+"_"+old_line);
					sp_old.style.background='';
					sp_old.style.color='#000000';
				}
				e.cancelBubble = true;
				if (e.stopPropagation) e.stopPropagation();
			}
			break;
		case 38:	//Flèche haut
			if (document.getElementById("d"+id).style.display=="block") {
				old_line=line[id];
				if (line[id]>0) line[id]--;
				if (line[id]>0) {
					sp=document.getElementById("l"+id+"_"+line[id]);
					sp.style.background='#000088';
					sp.style.color='#FFFFFF';
				}
				if (old_line) {
					sp_old=document.getElementById("l"+id+"_"+old_line);
					sp_old.style.background='';
					sp_old.style.color='#000000';
				}
			}
			break;
		case 9:		//Tab
			document.getElementById("d"+id).style.display='none';
			not_show[id]=true;
			break;
		case 13:	//Enter
			if ((line[id])&&(document.getElementById("d"+id).style.display=="block")) {
				var sp=document.getElementById("l"+id+"_"+line[id]);
				var text=sp.firstChild.nodeValue;
				var autfield=document.getElementById(id).getAttribute("autfield");
				var callback=document.getElementById(id).getAttribute("callback");
				var div_cache=document.getElementById("c"+id+"_"+line[id]);
				var position_curseur = get_pos_curseur(document.getElementById(id));
				var insert_between_separator = document.getElementById(id).getAttribute("separator");
				var old_text = document.getElementById(id).value;
				if (autfield) {
					var autid=sp.getAttribute("autid");
					document.getElementById(autfield).value=autid;
					var thesid = sp.getAttribute("thesid");
					if(thesid >0){
						var theselector = document.getElementById(autfield.replace('field','fieldvar').replace("_id","")+"[id_thesaurus][]");
						if(theselector){
							for (var i=1 ; i< theselector.options.length ; i++){
								if (theselector.options[i].value == thesid){
									theselector.options[i].selected = true;
									break;
								}
							}
						}
					}
					var type = sp.getAttribute("typeuri");
					if (type && (autfield.indexOf('value', 0) != -1)) document.getElementById(autfield.replace('value','type')).value = type;
				}
				
				if(div_cache){
					if (insert_between_separator != '') {
						if ( typeof position_curseur != 'undefined' ) {
							var sep_end = old_text.length;
							var tmp_text = old_text;
							if (old_text.indexOf(insert_between_separator,position_curseur) != '-1') {
								sep_end = old_text.indexOf(insert_between_separator,position_curseur);
								tmp_text = old_text.substr(0,sep_end);
							}
							var sep_start = 0;
							if (tmp_text.lastIndexOf(insert_between_separator,position_curseur) != '-1') {
								sep_start = tmp_text.lastIndexOf(insert_between_separator,position_curseur)+1;
							}
							document.getElementById(id).value = old_text.substr(0,sep_start)+div_cache.firstChild.nodeValue+old_text.substr(sep_end);
							text = document.getElementById(id).value;
						}
					} else {
						document.getElementById(id).value=div_cache.firstChild.nodeValue;
					}
					var position = position_curseur+text.length;
					var taille_search = div_cache.getAttribute('nbcar');
					setCursorPosition(document.getElementById(id), position-taille_search);
				} else {
					document.getElementById(id).value=text;
				}
				document.getElementById("d"+id).style.display='none';
				not_show[id]=true;
				if(e.preventDefault){
					e.preventDefault();//Firefox : Si je suis dans une liste je ne veux pas valider le formulaire quand je clic sur entrée 
				}else{
					e.returnValue = false;//IE : Si je suis dans une liste je ne veux pas valider le formulaire quand je clic sur entrée 
				}
			}
			e.cancelBubble = true;
			if (e.stopPropagation) e.stopPropagation();
			if (callback) window[callback](id);
			break;
			
		default:
			if ((last_word[id]==document.getElementById(id).value)&&(last_word[id])) break;
			if ((document.getElementById(id).value!="")&&(!not_show[id])) {
				ajax_creerRequete(id);
				if (requete[id]) {
					last_word[id]=document.getElementById(id).value;
					ajax_get_info(id);
				}
			} else {
				document.getElementById("d"+id).style.display='none';
				if (document.getElementById(id).value=="") not_show[id]=true;
			}
			last_word[id]=document.getElementById(id).value;
			break;
	}

	if(touche.indexOf(e.keyCode) > -1){
		switch (e.keyCode){
			case 40:	//Flèche bas
				if ((document.getElementById("d"+id).style.display=="none")&&(document.getElementById(id).value!="")) {
					p=document.getElementById(id);
					poss=findPos(p);
					poss[1]+=p.clientHeight;
					document.getElementById("d"+id).style.left=poss[0]+"px";
					document.getElementById("d"+id).style.top=poss[1]+"px";
					document.getElementById("d"+id).style.display='block';
					not_show[id]=false;
					ajax_creerRequete(id);
					if (requete[id]) {
						last_word[id]=document.getElementById(id).value;
						ajax_get_info(id);
					}
					e.cancelBubble = true;
					if (e.stopPropagation) e.stopPropagation();
				}
				break;
			case 113:	//F2
				position_curseur = get_pos_curseur(document.getElementById(id));
				if ((document.getElementById("d"+id).style.display=="none")&&(document.getElementById(id).value!="")) {
					p=document.getElementById(id);
					poss=findPos(p);
					poss[1]+=p.clientHeight;
					document.getElementById("d"+id).style.left=poss[0]+"px";
					document.getElementById("d"+id).style.top=poss[1]+"px";
					document.getElementById("d"+id).style.display='block';
					not_show[id]=false;
					ajax_creerRequete(id);
					if (requete[id]) {
						last_word[id]=document.getElementById(id).value;
						ajax_get_info(id);
					}
					e.cancelBubble = true;
					if (e.stopPropagation) e.stopPropagation();
				}
				break;
			default:
				break;
		}		
	}
}



function get_pos_curseur(textArea){
	if ( typeof textArea.selectionStart != 'undefined' )
 		return textArea.selectionStart;
 	// POUR IE
	textArea.focus();
 	var range = textArea.createTextRange();
 	range.moveToBookmark(document.selection.createRange().getBookmark());
 	range.moveEnd('character', textArea.value.length);
 	return textArea.value.length - range.text.length;

	
}

function ajax_creerRequete(id) {
	try {
		requete[id]=new XMLHttpRequest();
	} catch (essaimicrosoft) {
		try {
			requete[id]=new ActiveXObject("Msxml2.XMLHTTP");
		} catch (autremicrosoft) {
			try {
				requete[id]=new ActiveXObject("Microsoft.XMLHTTP");
			} catch (echec) {
				requete[id]=null;
			}
		}
	}
}

function ajax_show_info(id) {
	if (requete[id].readyState==4) {
		if (requete[id].status=="200") {
			cadre=document.getElementById("d"+id);
			cadre.innerHTML=requete[id].responseText;
			line[id]=0;
			if (requete[id].responseText=="") {
				document.getElementById("d"+id).style.display='none';
			} else {
				p=document.getElementById(id);
				poss=findPos(p);
				poss[1]+=p.clientHeight+1;
				document.getElementById("d"+id).style.left=poss[0]+"px";
				document.getElementById("d"+id).style.top=poss[1]+"px";
				document.getElementById("d"+id).style.display='block';
			}
		} //else alert("Erreur : le serveur a répondu "+requete.responseText);
	}
}

function ajax_get_info(id) {
	var autexclude = '' ;
	var autfield = '' ;
	var linkfield = '' ;
	var typdoc = '' ;
	var listfield = '';
	var att_id_filter = '' ;
	var param1 = '' ;
	var param2 = '' ;
	
	
	requete[id].open("POST","ajax_selector.php",true);
	requete[id].onreadystatechange=function() { ajax_show_info(id) };
	requete[id].setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	
	if (document.getElementById(id).getAttribute("autexclude")) autexclude = document.getElementById(id).getAttribute("autexclude") ;
	if (document.getElementById(id).getAttribute("linkfield")) linkfield = document.getElementById(document.getElementById(id).getAttribute("linkfield")).value ;
	if (document.getElementById(id).getAttribute("autfield")) autfield = document.getElementById(id).getAttribute("autfield") ;
	if (document.getElementById(id).getAttribute("param1")) param1 = document.getElementById(id).getAttribute("param1") ;
	if (document.getElementById(id).getAttribute("param2")) param2 = document.getElementById(id).getAttribute("param2") ;
	if (document.getElementById(id).getAttribute("typdoc")) typdoc = document.getElementById(document.getElementById(id).getAttribute("typdoc")).value ;
	if (att_id_filter=document.getElementById(id).getAttribute("att_id_filter"));
	if (document.getElementById(id).getAttribute("listfield")){
		var reg = new RegExp("[,]","g");
		var tab = (document.getElementById(id).getAttribute("listfield")).split(reg);		
		for(var k=0;k<tab.length;k++){
			listfield = listfield + "&"+tab[k]+"="+(document.getElementById(tab[k]).value);
		}
	}
	requete[id].send("datas="+encode_URL(document.getElementById(id).value)+"&id="+encode_URL(id)+"&completion="+encode_URL(document.getElementById(id).getAttribute("completion"))+"&persofield="+encode_URL(document.getElementById(id).getAttribute("persofield"))+"&autfield="+encode_URL(autfield)+"&autexclude="+encode_URL(autexclude)+"&param1="+encode_URL(param1)+"&param2="+encode_URL(param2)+"&linkfield="+encode_URL(linkfield)+"&att_id_filter="+encode_URL(att_id_filter)+"&typdoc="+encode_URL(typdoc)+"&pos_cursor="+encode_URL(get_pos_curseur(document.getElementById(id)))+listfield);
}

function validation(e){
	if (!e) var e = window.event;
	if (e.keyCode!=undefined) key = e.keyCode;
		else if (e.which) key = e.which;
	
	if (e.target) 
			var id=e.target.getAttribute("id"); 
	else var id=e.srcElement.getAttribute("id");
	
	if(((key == 13) && (not_show[id] == false)) || (key == 40)){
		//On annule tous les comportements par défaut du navigateur
		if (e.stopPropagation) {
			e.preventDefault();
			e.stopPropagation();
		} else {
			e.cancelBubble = true;
			e.returnValue=false;
		}
	}	
}


function ajax_remove_elements(id){
	
}