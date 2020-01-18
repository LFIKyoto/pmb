<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_agenda_datasource_agenda.class.php,v 1.12.2.4 2019-11-04 10:54:54 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_agenda_datasource_agenda extends cms_module_common_datasource{

	public function __construct($id=0){
		parent::__construct($id);
	}
	/*
	 * On d�fini les s�lecteurs utilisable pour cette source de donn�e
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_env_var",
			"cms_module_agenda_selector_calendars_date",
			"cms_module_agenda_selector_calendars"
		);
	}

	/*
	 * R�cup�ration des donn�es de la source...
	 */
	public function get_datas(){
		$datas = array();
		$selector = $this->get_selected_selector();
		$today = time();
		$date_time = date('Y-m-d', $today);

		switch($this->parameters['selector']){
			//devrait �tre le seul survivant...
			case "cms_module_agenda_selector_calendars" :
				if($selector){
					$calendars = array();
					$query = "select managed_module_box from cms_managed_modules join cms_cadres on id_cadre = '".($this->cadre_parent*1)."' and cadre_object = managed_module_name";
					$result = pmb_mysql_query($query);
					$events=array();
					if(pmb_mysql_num_rows($result)){
						$box = pmb_mysql_result($result,0,0);
						$infos =unserialize($box);
						
						//On test s'il s'agit du nouveau format de calendrier comportant les param�tres old_event ou futur_event
						$module_parameters = $selector->get_value();
						if (!isset($module_parameters['old_event'])) {
						    $old_event = false;
						    $futur_event = true;
						    $calendars = $module_parameters;
						} else {
						    $old_event = $module_parameters['old_event'];
						    $futur_event = $module_parameters['futur_event'];
						    $calendars = $module_parameters['calendars'];
						}
						foreach($calendars as $calendar){
							$elem = $infos['module']['calendars'][$calendar];
							$query="select id_article from cms_articles where article_num_type = '".($elem['type']*1)."'";
							$result = pmb_mysql_query($query);
							if($result && pmb_mysql_num_rows($result)){
								$articles = array();
								while($row = pmb_mysql_fetch_object($result)){
									$articles[]=$row->id_article;
								}
								$articles = $this->filter_datas("articles",$articles);
								foreach($articles as $article){
									$art = new cms_article($article);
									$event = $art->format_datas();
									foreach($event->fields_type as $field){
										if($field['id'] == $elem['start_date']){
											$event->event_start = $field['values'][0];
											$event->event_start['time'] = mktime(0,0,0,substr($field['values'][0]['value'],5,2),substr($field['values'][0]['value'],8,2),substr($field['values'][0]['value'],0,4));
										}
										if($field['id'] == $elem['end_date']){
											$event->event_end = $field['values'][0];
											$event->event_end['time'] = mktime(0,0,0,substr($field['values'][0]['value'],5,2),substr($field['values'][0]['value'],8,2),substr($field['values'][0]['value'],0,4));
										}
									}
									$event->id_type = $elem['type'];
									$event->color = $elem['color'];
									$event->calendar = $elem['name'];
								
									//Evenement sur une p�riode
									if (!empty($event->event_start) && !empty($event->event_end)) {
									    if($event->event_start['value']<=$date_time && $event->event_end['value']>=$date_time) {
									        $current_events[] = $event;
									    } elseif ($event->event_start['value']<$date_time && $event->event_end['value']<$date_time) {
									        $old_events[] = $event;
									    } elseif ($event->event_start['value']>$date_time && $event->event_end['value']>$date_time) {
									        $futur_events[] = $event;
									    }
									    //Evenement ponctuel
									} elseif (!empty($event->event_start)) {
									    if($event->event_start['value']==$date_time) {
									        $current_events[] = $event;
									    } elseif ($event->event_start['value']<$date_time) {
									        $old_events[] = $event;
									    } elseif ($event->event_start['value']>$date_time) {
									        $futur_events[] = $event;
									    }
									}
								}
							}
						}
					}
					//On conditionne l'ajout des �v�nements en fonction des param�tres
					if ($old_event && !empty($old_events)) {
					    $events = array_merge($events,$old_events);
					}
					if (!empty($current_events)){
					    $events = array_merge($events,$current_events);
					}
					if ($futur_event && !empty($futur_events)) {
					    $events = array_merge($events,$futur_events);
					}
					usort($events,array($this,"sort_event"));
					return array('events'=>$events);
				}
				break;
			case "cms_module_common_selector_env_var" :
				if($selector){
					$art = new cms_article($selector->get_value());
					$event = $art->format_datas();
					//allons chercher les infos du calendrier associ� � cet �v�nement
					$query = "select managed_module_box from cms_managed_modules join cms_cadres on id_cadre = '".($this->cadre_parent*1)."' and cadre_object = managed_module_name";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$box = pmb_mysql_result($result,0,0);
						$infos =unserialize($box);
						foreach($infos['module']['calendars'] as $calendar){
							if($calendar['type'] == $art->num_type){
								foreach($event->fields_type as $field){
									if($field['id'] == $calendar['start_date']){
										$event->event_start = $field['values'][0];
										$event->event_start['time'] = mktime(0,0,0,substr($field['values'][0]['value'],5,2),substr($field['values'][0]['value'],8,2),substr($field['values'][0]['value'],0,4));
									}
									if($field['id'] == $calendar['end_date']){
										$event->event_end = $field['values'][0];
										$event->event_end['time'] = mktime(0,0,0,substr($field['values'][0]['value'],5,2),substr($field['values'][0]['value'],8,2),substr($field['values'][0]['value'],0,4));
									}
								}
								$event->id_type = $calendar['type'];
								$event->color = $calendar['color'];	
								$event->calendar = $calendar['name'];
								break;
							}
						}
					}
					return $event;
				}
				break;
			case "cms_module_agenda_selector_calendars_date" :
				if($selector){
				    $events = array();
					$query = "select managed_module_box from cms_managed_modules join cms_cadres on id_cadre = '".($this->cadre_parent*1)."' and cadre_object = managed_module_name";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$box = pmb_mysql_result($result,0,0);
						$infos =unserialize($box);
						
						//On test s'il s'agit du nouveau format de calendrier comportant les param�tres old_event ou futur_event
						$module_parameters = $selector->get_value();
                		if (!isset($module_parameters['calendars']['old_event'])) {
                		    $old_event = false;
                		    $futur_event = true;
                		    $datas = $module_parameters;
                		} else {
    						$old_event = $module_parameters['calendars']['old_event'];
                		    $futur_event = $module_parameters['calendars']['futur_event'];
                		    $datas = $module_parameters['calendars'];
                		    $datas['date'] = $module_parameters['date'];
                		}
                        $time = $today;            
                        $selected_date = false;
						if(!empty($datas['date'])){
        					$time = mktime(0,0,0,substr($datas['date'],5,2),substr($datas['date'],8,2),substr($datas['date'],0,4));						    
                            $selected_date = true;
						}
        			    $date_time = date('Y-m-d', $time);
						foreach($datas['calendars'] as $calendar){
							$elem = $infos['module']['calendars'][$calendar];
							$query="select id_article from cms_articles where article_num_type = '".($elem['type']*1)."'";
							$result = pmb_mysql_query($query);
							if(pmb_mysql_num_rows($result)){
								$articles = array();
								while($row = pmb_mysql_fetch_object($result)){
									$articles[]=$row->id_article;
								}
								$articles = $this->filter_datas("articles",$articles);
								if(is_array($articles)){
									foreach($articles as $article){
										$art = new cms_article($article);
										$event = $art->format_datas();
										foreach($event->fields_type as $field){
											if($field['id'] == $elem['start_date']){
												$event->event_start = $field['values'][0];
												$event->event_start['time'] = mktime(0,0,0,substr($field['values'][0]['value'],5,2),substr($field['values'][0]['value'],8,2),substr($field['values'][0]['value'],0,4));
											}
											if($field['id'] == $elem['end_date']){
												$event->event_end = $field['values'][0];
												$event->event_end['time'] = mktime(0,0,0,substr($field['values'][0]['value'],5,2),substr($field['values'][0]['value'],8,2),substr($field['values'][0]['value'],0,4));
											}
										}
										$event->id_type = $elem['type'];
										$event->color = $elem['color'];
										$event->calendar = $elem['name'];

										//Evenement sur une p�riode 
										if (!empty($event->event_start) && !empty($event->event_end)) {
										    if($event->event_start['value']<=$date_time && $event->event_end['value']>=$date_time) {
										       $current_events[] = $event;
										    } elseif ($event->event_start['value']<$date_time && $event->event_end['value']<$date_time) {
										       $old_events[] = $event;
										    } elseif ($event->event_start['value']>$date_time && $event->event_end['value']>$date_time) {
										       $futur_events[] = $event;
										    }
										//Evenement ponctuel
										} elseif (!empty($event->event_start)) {
										    if($event->event_start['value']==$date_time) {
										        $current_events[] = $event;
										    } elseif ($event->event_start['value']<$date_time) {
										        $old_events[] = $event;
										    } elseif ($event->event_start['value']>$date_time) {
										        $futur_events[] = $event;
										    }
										}
									}
								}
							}
						}
					}
					//On modifie l'�tat du flag old_event si une date est pass�e en param�tres Get
					if ($selected_date) {
					    $old_event = false;
					}
					//On conditionne l'ajout des �v�nements en fonction des param�tres
					if ($old_event && !empty($old_events)) {
					    $events = array_merge($events,$old_events);
					} 
					if (!empty($current_events)){
					    $events = array_merge($events,$current_events);
					}
					if ($futur_event && !empty($futur_events)) {
					    $events = array_merge($events,$futur_events);
					}
					usort($events,array($this,"sort_event"));
					return array('events'=>$events);
				}
				break;
		}
	}
	
	
	public static function sort_event($a,$b){
		if(isset($a->event_start) && ($a->event_start['time'] > $b->event_start['time'])){
			return 1;
		}else if(isset($a->event_start) && ($a->event_start['time'] == $b->event_start['time'])){
			if(isset($a->event_end) && ($a->event_end['time'] > $b->event_end['time'])){
				return 1;
			}else{
				return -1;
			}
		}else{
			return -1;
		}
	}
	
	public function get_format_data_structure($type='event'){
		$format_datas = array();
		switch($type){
			//event
			case "event" :
				$format_datas = cms_article::get_format_data_structure("article");
				$format_datas[] = array(
					'var' => "event_start",
					'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_start_desc'],
					'children' => array(
						array(
							'var' => "event_start.format_value",
							'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_start_format_value_desc'],
						),
						array(
							'var' => "event_start.value",
							'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_start_value_desc'],
						),
						array(
							'var' => "event_start.time",
							'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_start_time_desc'],
						)
					)
				);
				$format_datas[] = array(
					'var' => "event_end",
					'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_end_desc'],
					'children' => array(
						array(
							'var' => "event_end.format_value",
							'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_end_format_value_desc'],
						),
						array(
							'var' => "event_end.value",
							'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_end_value_desc'],
						),
						array(
							'var' => "event_end.time",
							'desc' => $this->msg['cms_module_agenda_datasource_agenda_event_end_time_desc'],
						)
					)
				);
				$format_datas[] = array(
					'var' => "id_type",
					'desc' => $this->msg['cms_module_agenda_datasource_agenda_id_type_desc']
				);
				$format_datas[] = array(
					'var' => "color",
					'desc' => $this->msg['cms_module_agenda_datasource_agenda_color_desc']
				);
				$format_datas[] = array(
					'var' => "calendar",
					'desc' => $this->msg['cms_module_agenda_datasource_agenda_calendar_desc']
				);
				break;
			case "eventslist" :
				$format_event = $this->get_format_data_structure("event");
				$format_datas[] = array(
					'var' => "events",
					'desc'=> $this->msg['cms_module_agenda_datasource_agenda_events_desc'],
					'children' => $this->prefix_var_tree($format_event,"events[i]")
				); 
				break;	
		}							
		return $format_datas;
	}
}











