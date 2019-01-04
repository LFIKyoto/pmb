<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cashdesk_list.class.php,v 1.12 2018-12-19 13:59:19 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/cashdesk/cashdesk.tpl.php");
require_once($class_path."/cashdesk/cashdesk.class.php");

class cashdesk_list {	
	public $cashdesk_list=array(); // liste des caisses
	
	public function __construct(){
		$this->fetch_data();		
	}
	
	protected function fetch_data(){
		// les data...	
		$this->cashdesk_list=array();	
		$rqt = "select * from cashdesk order by cashdesk_name";
		$res = pmb_mysql_query($rqt);
		$i=0;
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$this->cashdesk_list[$i]['id'] = $row->cashdesk_id;
				$this->cashdesk_list[$i]['name'] = $row->cashdesk_name;
				$i++;
			}
		}
	}

	public function get_form(){
		global $msg;
		global $cashdesk_list_form, $charset;
		
		$form = "";
		$parity = 0;
		foreach ($this->cashdesk_list as $index =>$cashdesk){
			if ($parity++ % 2)	$pair_impair = "even"; else $pair_impair = "odd";			
			$form.= "
				<tr class='$pair_impair' onmouseout=\"this.className='$pair_impair'\" onmouseover=\"this.className='surbrillance'\" style='cursor: pointer'>
					<td onmousedown=\"document.location='./admin.php?categ=finance&sub=cashdesk&action=edit&id=".$cashdesk['id']."'\" >".htmlentities($cashdesk['name'],ENT_QUOTES, $charset)."</td>
				</tr>
			";
		}		
		$cashdesk_list_form = str_replace('!!cashdesk_list!!', $form, $cashdesk_list_form);
		return $cashdesk_list_form;
	}

	public function get_form_summarize(){
		global $msg;
		global $cashdesk_list_form_summarize, $charset;
		global $cashdesk_filter,$start_date, $stop_date;
		
		if(!count($this->cashdesk_list))return "";
		if(!$cashdesk_filter)$cashdesk_filter=array();
		if(!$cashdesk_filter[0])$cashdesk_filter=array();
		
		if(!count($cashdesk_filter) )$selected= " selected=\"selected\" ";
		$cashdesk_filter_form="<select  name='cashdesk_filter[]' multiple >
			<option value='' $selected >--</option>\n";
		foreach ($this->cashdesk_list as $index =>$cashdesk){
			if(in_array($cashdesk['id'],$cashdesk_filter))$selected= " selected=\"selected\" ";
			else $selected="";
			$cashdesk_filter_form.="<option value='".$cashdesk['id']."' $selected >".htmlentities($cashdesk['name'],ENT_QUOTES, $charset)."</option>\n";
		}
		$cashdesk_filter_form.="</select>";
		
		$found=0;
		$tt_realisee_no=0;
		$tt_realisee=0;
		$tt_encaissement_no=0;
		$tt_encaissement=0;
		foreach ($this->cashdesk_list as $index =>$cashdesk){		
			if(count($cashdesk_filter) ){
				if(! in_array($cashdesk['id'], $cashdesk_filter)) continue;					
			}			
			$cashdesk_info=new cashdesk($cashdesk['id']);
			$all_transactions=$cashdesk_info->summarize($start_date, $stop_date, $transactype,$encaissement);
			
			foreach($all_transactions as $transactions){
				if ($parity++ % 2)	$pair_impair = "even"; else $pair_impair = "odd";
				$form.= "
				<tr class='$pair_impair' onmouseout=\"this.className='$pair_impair'\" onmouseover=\"this.className='surbrillance'\" style='cursor: pointer'>
					<td onmousedown=\"document.location='./admin.php?categ=finance&sub=cashdesk&action=edit&id=".$cashdesk['id']."'\" >".htmlentities($cashdesk['name'],ENT_QUOTES, $charset)."</td>
					<td onmousedown=\"document.location='./admin.php?categ=finance&sub=transactype&action=edit&id=".$transactions['id']."'\" >".htmlentities($transactions['name'],ENT_QUOTES, $charset)."</td>
					<td>".htmlentities($this->format_price($transactions['unit_price']),ENT_QUOTES, $charset)."</td>
					<td>".htmlentities($this->format_price($transactions['montant']),ENT_QUOTES, $charset)."</td>
					<td>".htmlentities($this->format_price($transactions['realisee_no']),ENT_QUOTES, $charset)."</td>			
					<td>".htmlentities($this->format_price($transactions['realisee']),ENT_QUOTES, $charset)."</td>			
					<td>".htmlentities($this->format_price($transactions['encaissement_no']),ENT_QUOTES, $charset)."</td>			
					<td>".htmlentities($this->format_price($transactions['encaissement']),ENT_QUOTES, $charset)."</td>
				</tr>
				";
				$tt_realisee_no+=$transactions['realisee_no'];
				$tt_realisee+=$transactions['realisee'];
				$tt_encaissement_no+=$transactions['encaissement_no'];
				$tt_encaissement+=$transactions['encaissement'];

				$found++;
			}			
		}
		$formall=str_replace('!!cashdesk_list!!', $form, $cashdesk_list_form_summarize);		
		$formall=str_replace('!!cashdesk_filter!!', $cashdesk_filter_form, $formall);			
		$formall=str_replace('!!start_date!!', $start_date, $formall);				
		$formall=str_replace('!!stop_date!!', $stop_date, $formall);	
			
		$formall=str_replace('!!realisee_no!!',$this->format_price($tt_realisee_no) , $formall);
		$formall=str_replace('!!realisee!!',$this->format_price($tt_realisee) , $formall);
		$formall=str_replace('!!encaissement_no!!',$this->format_price($tt_encaissement_no) , $formall);
		$formall=str_replace('!!encaissement!!',$this->format_price($tt_encaissement) , $formall);	
		
		$formall=str_replace('!!transaction_filter!!', $transaction_filter_form, $formall);		
		
		return $formall;
	}
	
	public function get_html_summarize(){
		global $msg;
		global $charset,$cashdesk_list_form_summarize_table,$titre_page;
		global $cashdesk_filter,$start_date, $stop_date;
		
		if(!count($this->cashdesk_list))return "";
		if(!$cashdesk_filter)$cashdesk_filter=array();
		if(!$cashdesk_filter[0])$cashdesk_filter=array();		
		
		$found=0;
		$tt_realisee_no=0;
		$tt_realisee=0;
		$tt_encaissement_no=0;
		$tt_encaissement=0;
		foreach ($this->cashdesk_list as $index =>$cashdesk){		
			if(count($cashdesk_filter) ){
				if(! in_array($cashdesk['id'], $cashdesk_filter)) continue;
			}
			$cashdesk_info=new cashdesk($cashdesk['id']);
			$all_transactions=$cashdesk_info->summarize($start_date, $stop_date, $transactype,$encaissement);
				
			foreach($all_transactions as $transactions){
				$form.= "
				<tr >
					<td>".htmlentities($cashdesk['name'],ENT_QUOTES, $charset)."</td>
					<td>".htmlentities($transactions['name'],ENT_QUOTES, $charset)."</td>
					<td>".htmlentities($this->format_price($transactions['unit_price']),ENT_QUOTES, $charset)."</td>
					<td>".htmlentities($this->format_price($transactions['montant']),ENT_QUOTES, $charset)."</td>
					<td>".htmlentities($this->format_price($transactions['realisee_no']),ENT_QUOTES, $charset)."</td>
					<td>".htmlentities($this->format_price($transactions['realisee']),ENT_QUOTES, $charset)."</td>
					<td>".htmlentities($this->format_price($transactions['encaissement_no']),ENT_QUOTES, $charset)."</td>
					<td>".htmlentities($this->format_price($transactions['encaissement']),ENT_QUOTES, $charset)."</td>
				</tr>
				";
				$tt_realisee_no+=$transactions['realisee_no'];
				$tt_realisee+=$transactions['realisee'];
				$tt_encaissement_no+=$transactions['encaissement_no'];
				$tt_encaissement+=$transactions['encaissement'];
		
				$found++;
			}
		}		
		$formall=str_replace('!!cashdesk_list!!', $form, $cashdesk_list_form_summarize_table);
		$formall=str_replace('!!realisee_no!!',$this->format_price($tt_realisee_no) , $formall);
		$formall=str_replace('!!realisee!!',$this->format_price($tt_realisee) , $formall);
		$formall=str_replace('!!encaissement_no!!',$this->format_price($tt_encaissement_no) , $formall);
		$formall=str_replace('!!encaissement!!',$this->format_price($tt_encaissement) , $formall);
		return $formall;
	}
	
	public function get_excel_summarize(){
		global $msg, $class_path;
		global $charset,$fichier_temp_nom,$titre_page;
		global $cashdesk_filter,$start_date, $stop_date;
		
		if(!count($this->cashdesk_list))return "";
		if(!$cashdesk_filter)$cashdesk_filter=array();
		if(!$cashdesk_filter[0])$cashdesk_filter=array();		
		
		require_once ($class_path."/spreadsheet.class.php");
		$worksheet = new spreadsheet();
		$worksheet->write(0,0,$titre_page);		
		$i=2;
		$j=2;
		$worksheet->write($i,$j++,$msg["cashdesk_edition_name"]);
		$worksheet->write($i,$j++,$msg["cashdesk_edition_transac_name"]);
		$worksheet->write($i,$j++,$msg["cashdesk_edition_transac_unit_price"]);
		$worksheet->write($i,$j++,$msg["cashdesk_edition_transac_montant"]);
		$worksheet->write($i,$j++,$msg["cashdesk_edition_transac_realisee_no"]);
		$worksheet->write($i,$j++,$msg["cashdesk_edition_transac_realisee"]);
		$worksheet->write($i,$j++,$msg["cashdesk_edition_transac_encaissement_no"]);
		$worksheet->write($i,$j++,$msg["cashdesk_edition_transac_encaissement"]);
		$i++;
		foreach ($this->cashdesk_list as $index =>$cashdesk){		
			if(count($cashdesk_filter) ){
				if(! in_array($cashdesk['id'], $cashdesk_filter)) continue;					
			}			
			$cashdesk_info=new cashdesk($cashdesk['id']);
			$all_transactions=$cashdesk_info->summarize($start_date, $stop_date, $transactype,$encaissement);
						
			if(!count($all_transactions) ) continue;	
						
			foreach($all_transactions as $transactions){	
				$j=2;
				$worksheet->write($i,$j++,$cashdesk['name']);
				$worksheet->write($i,$j++,$transactions['name']);
				$worksheet->write($i,$j++,$this->format_price($transactions['unit_price']));
				$worksheet->write($i,$j++,$this->format_price($transactions['montant']));
				$worksheet->write($i,$j++,$this->format_price($transactions['realisee_no']));
				$worksheet->write($i,$j++,$this->format_price($transactions['realisee']));
				$worksheet->write($i,$j++,$this->format_price($transactions['encaissement_no']));
				$worksheet->write($i,$j++,$this->format_price($transactions['encaissement']));
				$i++;
			}
		}	
		$worksheet->download('caisse.xls');
	}
	
	public function format_price($price) {
		global $pmb_fine_precision;
		
		if (!$pmb_fine_precision) $pmb_fine_precision=2;
		return 	number_format($price + 0, $pmb_fine_precision, '.', ' ');
	}
	
	public function proceed(){
		global $action;
		
		switch($action) {
			case 'add':
				break;				
    		default:
				break;
		}
	}
}