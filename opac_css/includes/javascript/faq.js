// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq.js,v 1.1 2014-04-02 12:29:07 arenou Exp $

function faq_expand_collaspe(id){
	if(id){
		var answer = document.getElementById("child_question_"+id);
		if(answer){
			switch(answer.style.display){
				case "block" :
					answer.style.display = "none";
					break;
				case "none" :
				default :
					answer.style.display = "block";
					break;
			}
			
		}
	}
}

function faq_collapse_all_questions(){
	var childs = document.getElementsByClassName("faq_child");
	for(var i=0 ; i<childs.length ; i++){
		childs[i].style.display = "none";
	}
}

function faq_expand_all_questions(){
	var childs = document.getElementsByClassName("faq_child");
	for(var i=0 ; i<childs.length ; i++){
		childs[i].style.display = "block";
	}	
}