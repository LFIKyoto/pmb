<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_agenda_view_eventslist.class.php,v 1.5 2014-11-17 17:00:53 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_agenda_view_eventslist extends cms_module_common_view_articleslist{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "
<div>
{% for event in events %}
<h3>
{% if event.event_start.format_value %}
 {% if event.event_end.format_value %}
du {{event.event_start.format_value}} au {{event.event_end.format_value}}
 {% else %}
le {{event.event_start.format_value}}
 {% endif %}
{% endif%} : {{event.title}}
</h3>
<blockquote>
<img src='{{event.logo.large}}'/>
<p>{{event.resume}}<br/><a href='{{event.link}}'>plus d'infos...<a/></p>
</blockquote>
{% endfor %}
</div>";
	}
	
	public function render($datas){
		$render_datas = array();
		$render_datas['title'] = "Liste d'évènements";
		$render_datas['events'] = array();
		foreach($datas['events'] as $event){
			$event['link'] = $this->get_constructed_link("article",$event['id']);
			$render_datas['events'][]=$event;
		}
		//on rappelle le tout...
		return cms_module_common_view_django::render($render_datas);
	}
	
	public function get_format_data_structure(){
		$datasource = new cms_module_agenda_datasource_agenda();
		$format_data = $datasource->get_format_data_structure("eventslist");
		$format_data[0]['children'][] = array(
			'var' => "events[i].link",
			'desc'=> $this->msg['cms_module_agenda_view_evenslist_link_desc']
		);
		$format_data[] = array(
			'var' => "title",
			'desc'=> $this->msg['cms_module_agenda_view_evenslist_title_desc']
		);
		$format_data = array_merge($format_data,cms_module_common_view_django::get_format_data_structure());
		return $format_data;
	}
}