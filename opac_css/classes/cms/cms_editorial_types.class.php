<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial_types.class.php,v 1.7 2015-04-03 14:32:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/cms/cms_editorial_types.tpl.php");

class cms_editorial_types {
	public $element;
	public $types = array();	//tableau des types existant
	
	public function __construct($element){
		$this->element = $element;
	}

	protected function fetch_data(){
		global $msg;
		$rqt = "select * from cms_editorial_types where editorial_type_element = '".$this->element."_generic'";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			$row = pmb_mysql_fetch_object($res);
			$type = array(
				'id' => $row->id_editorial_type,
				'element' => $row->editorial_type_element,
				'label' => $msg['editorial_content_type_fieldslist_'.$row->editorial_type_element.'_label'],
				'comment' => $row->editorial_type_comment
			);
			$fields = new cms_editorial_parametres_perso($row->id_editorial_type);
			$type['fields'] = $fields->t_fields;
			$this->types[] = $type;
		}
		$rqt = "select * from cms_editorial_types where editorial_type_element = '".$this->element."' order by editorial_type_label";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$type = array(
					'id' => $row->id_editorial_type,
					'element' => $row->editorial_type_element,
					'label' => $row->editorial_type_label,
					'comment' => $row->editorial_type_comment
				);
				$fields = new cms_editorial_parametres_perso($row->id_editorial_type);
				$type['fields'] = $fields->t_fields;
				$this->types[] = $type;
			}
		}
	}

	public function get_types(){
		if(!$this->types) {
			$this->fetch_data();
		}
		return $this->types;
	}
	
	public static function get_type($id){
		$rqt = "select * from cms_editorial_types where editorial_type_element = '".$this->element."' and id_editorial_type = ".$id;
		$res = pmb_mysql_query($rqt);
		$type = array();
		if($id && pmb_mysql_num_rows($res)){
			$row = pmb_mysql_fetch_object($res);
			$type = array(
				'id' => $row->id_editorial_type,
				'element' => $row->editorial_type_element,
				'label' => $row->editorial_type_label,
				'comment' => $row->editorial_type_comment
			);
		}
		return $type;		
	}

	public function get_selector_options($selected=0){
		global $msg,$charset;
		$options = "";
		$this->get_types();
		$options.= "
			<option value='0'".(!$selected ? "selected='selected'" : "").">".$msg['cms_editorial_form_type_choice']."</option>";	
		for($i=1 ; $i<count($this->types) ; $i++){
			$options.= "
			<option value='".$this->types[$i]['id']."'".($this->types[$i]['id']==$selected ? "selected='selected'" : "").">".htmlentities($this->types[$i]['label'],ENT_QUOTES,$charset)."</option>";	
		}
		return $options;
	}
	
	public function get_table($form_link=""){
		global $msg,$charset;
		global $type_list_empr;
		$this->get_types();
		
		if(!$form_link){
			$form_link="./admin.php?categ=cms_editorial&sub=type&elem=".$this->element."&action=edit";
		}
		
		$types =array();
		for($i=0 ; $i<count($this->types) ; $i++){
			if(strpos($this->types[$i]['element'], "generic") === false){
				$types[]=$this->types[$i];
			}
		}
		
		$table = "
		<table>
			<tr>
				<th>".$msg['editorial_content_type_label']."</th>
				<th>".$msg['editorial_content_type_comment']."</th>
				<th>".$msg['editorial_content_type_fields']."</th>
			</tr>";
		
		for($i=0 ; $i<count($types) ; $i++){
			$class = ($i%2 ? "odd":"even");
			$fields_list = "";
			foreach($types[$i]['fields'] as $field){
				$fields_list.= htmlentities($field['TITRE'],ENT_QUOTES,$charset)." (<i>".$type_list_empr[$field['TYPE']]."</i>)<br />";
			}
			
			$table.= "
			<tr class='".($i%2 ? "odd":"even")."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\">
				<td onclick='document.location=\"".$form_link."&id=".$this->types[$i]['id']."\"'style='cursor:pointer' >".htmlentities($types[$i]['label'],ENT_QUOTES,$charset)."</td>
				<td onclick='document.location=\"".$form_link."&id=".$this->types[$i]['id']."\"'style='cursor:pointer' >".htmlentities($types[$i]['comment'],ENT_QUOTES,$charset)."</td>
				<td>".$fields_list."<input type='button' class='bouton' value=' ".$msg['cms_editorial_type_fieldlist_edit']." ' onclick='document.location=\"./admin.php?categ=cms_editorial&sub=type&elem=".$this->element."&quoi=fields&type_id=".$types[$i]['id']."\"'/></td>
			</tr>";
		}
		$table.= "
		</table>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<input type='button' class='bouton' value='".$msg['editorial_content_type_add']."' onclick='document.location=\"".$form_link."&id=0\"'/>
			<input type='button' class='bouton' value='".$msg['editorial_content_type_edit_generic_field']."' onclick='document.location=\"./admin.php?categ=cms_editorial&sub=type&elem=".$this->element."_generic&quoi=fields\"'/>
		</div>";
		return $table;
	}
	
	public function get_form($id=0,$url=""){
		global $msg,$charset;
		global $cms_editorial_type_form;
		$this->get_types();
		
		if(!$url){
			$url="./admin.php?categ=cms_editorial&sub=type&elem=".$this->element;
		}
		
		$form =str_replace("!!action!!",$url,$cms_editorial_type_form);
		if($id){
			for($i=0 ; $i<count($this->types) ; $i++){
				if($this->types[$i]['id'] == $id){
					$type = $this->types[$i];
					break;
				}
			}
		}
		if($type['id']){
			$form = str_replace("!!form_title!!",$msg['editorial_content_type_edit'],$form);
			$form = str_replace("!!label!!",htmlentities($type['label'],ENT_QUOTES,$charset),$form);
			$form = str_replace("!!comment!!",htmlentities($type['comment'],ENT_QUOTES,$charset),$form);
			$form = str_replace("!!id!!",$type['id'],$form);
			$form = str_replace("!!bouton_supprimer!!","<input type='button' class='bouton' value=' ".$msg[63]." ' onclick='confirmation_delete(\"&action=delete&id=".$type['id']."\",\"".htmlentities($type['label'],ENT_QUOTES,$charset)."\")' />",$form);
			$form.= confirmation_delete($url);
		}else{
			$form = str_replace("!!form_title!!",$msg['editorial_content_type_add'],$form);	
			$form = str_replace("!!label!!","",$form);
			$form = str_replace("!!comment!!","",$form);
			$form = str_replace("!!id!!",0,$form);
			$form = str_replace("!!bouton_supprimer!!","",$form);
		}
		
		return $form;
	}
	
	public function save(){
		global $cms_editorial_type_label,$cms_editorial_type_comment,$cms_editorial_type_id;
		if($cms_editorial_type_id){
			$cms_editorial_type_id+=0;
			$query = "update cms_editorial_types set ";
			$clause = "where id_editorial_type = ".$cms_editorial_type_id;
		}else{
			$query = "insert into cms_editorial_types set ";
			$clause = "";
		}
		$query.= "
			editorial_type_element = '".$this->element."',
			editorial_type_label = '".$cms_editorial_type_label."',
			editorial_type_comment = '".$cms_editorial_type_comment."'";
		$query.= " ".$clause;
		pmb_mysql_query($query);
	}
	
	public function delete($id){
		global $msg,$charset;
		$id+=0;
		if($id){
			//on regarde si le type est utilisé
			$query = "select id_".$this->element." from cms_".$this->element."s where ".$this->element."_num_type = ".$id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$error = $msg['type_used'];
			}
		}
		if($error){
			print "
			<script type='text/javascript'>
				alert(\"".$msg['cant_delete'].". ".$error."\");
			</script>";
		}else{
			$fields = new cms_editorial_parametres_perso($id);
			$fields->delete_all();
			$query = "delete from cms_editorial_types where id_editorial_type = ".$id;
			pmb_mysql_query($query);
		}
	}
	
	public static function get_editable_form($id,$elem,$type_id){
		global $msg,$charset;
		
		$type = cms_editorial_types::get_type($type_id);
		//les champs perso...
		$obj = new cms_editorial_parametres_perso($type_id);
		$fields = $obj->show_editable_fields($id,$elem);
		$form="";
		for ($i=0; $i<count($fields["FIELDS"]); $i++) {
			$p=$fields["FIELDS"][$i];
			$form.="
			<div class='row'>
			<div class='row'><label for='".$p["NAME"]."' class='etiquette'>".htmlentities($p["TITRE"],ENT_QUOTES, $charset)."</label></div>
			<div class='row'>".$p["AFF"]."</div>
			</div><div class='row'>&nbsp;</div>";
		}
		if($form && count($type['exentions'])){
			$extension_form="<hr />";
		}else $extension_form="";
		
		$form.=$fields['CHECK_SCRIPTS'];
		
		//les extensions de formulaires
		for($i=0 ; $i<count($type['extensions']) ; $i++){
			$infos = explode(" ",$type['extensions'][$i]);
			$module = new $infos[0]();
			$extension_form.=$module->get_extension_form($infos[1],$elem,$id);
		}
		return $form.$extension_form;
	}
	
	public function save_type_form($num_type,$elem_id){
		//enregistrement des CP
		$type_fields = new cms_editorial_parametres_perso($num_type);
		$type_fields->rec_fields_perso($elem_id,$this->element);	
		//on passe aux extensions!
		$type = cms_editorial_types::get_type($num_type);
		for($i=0 ; $i<count($type['extensions']) ; $i++){
			$infos = explode(" ",$type['extensions'][$i]);
			$module = new $infos[0]();
			$extension_form.=$module->save_extension_form($infos[1],$this->element,$elem_id);
		}
	}
	
	public function duplicate_type_form($num_type,$elem_id,$duplicate_from_id){
		//duplication des CP
		$type_fields = new cms_editorial_parametres_perso($num_type);
		$type_fields->duplicate_fields_perso($elem_id, $duplicate_from_id,$this->element);
	}
	
	public function get_format_data_structure($full=true){
		global $msg,$charset;
		$fields_type = array();
		$this->get_types();
		foreach($this->types as $type){
			$infos= array(
				'var' => $type['label'],
				'desc'=> $type['comment']
			);
			foreach($type['fields'] as $field){
				$infos['children'][] = array(
					'var' => "fields_type.".$field['NAME'],
					'desc' => $field['TITRE'],
					'children' => array(
						array(
							'var' => "fields_type.".$field['NAME'].".id",
							'desc'=> $msg['cms_module_common_datasource_desc_fields_type_id'],
						),
						array(
							'var' => "fields_type.".$field['NAME'].".label",
							'desc'=> $msg['cms_module_common_datasource_desc_fields_type_label'],
						),
						array(
							'var' => "fields_type.".$field['NAME'].".values",
							'desc'=> $msg['cms_module_common_datasource_desc_fields_type_values'],
							'children' => array(
								array(
									'var'=> "fields_type.".$field['NAME'].".values[i].format_value",
									'desc' => $msg['cms_module_common_datasource_desc_fields_type_values_format_value'],
								),
								array(
									'var'=> "fields_type.".$field['NAME'].".values[i].value",
									'desc' => $msg['cms_module_common_datasource_desc_fields_type_values_value'],
								)
							)
						)
					)
				);
			}
			$fields_type[]=$infos;
		}
		return $fields_type;
	}
}