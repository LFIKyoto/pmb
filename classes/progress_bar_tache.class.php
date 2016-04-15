<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: progress_bar_tache.class.php,v 1.1 2011-07-29 12:32:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/progress_bar.class.php");

class progress_bar_tache extends progress_bar {
	var $percent='0';
	
	//Constructeur.	 $text
	function progress_bar_tache($percent='0') {
		
		$this->html_id = parent::$nb_instance;
		$this->percent= $percent;
		$this->show_tache();
		parent::$nb_instance++;
	}
	
	function show_tache(){
        print "<div class='row' id='progress_bar_".$this->html_id."' style='text-align:center; width:80%; border: 1px solid #000000; padding: 3px; z-index:1;'>
	            <div style='text-align:left; width:100%; height:20px;'>
	                <img id='progress_".$this->html_id."' src='images/jauge.png' style='width:".$this->percent."%; height:20px'/>
		            
		            <div style='text-align:center; position:relative; top: -25px; z-index:1'>
		                <span id='progress_text_".$this->html_id."'></span>".$this->percent."%
		                <span id='progress_percent_".$this->html_id."'></span>
		            </div>
		    	</div>
	        </div>";
        flush();
    }
}
