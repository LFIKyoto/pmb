<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: spreadsheet.class.php,v 1.6 2017-10-16 12:36:58 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/PHPExcel/PHPExcel.php");

class spreadsheet{
	
	private $objPHPExcel;
	
	protected $active_sheet;
	
	public function __construct(){
		global $base_path;
		
		$cache_dir = $base_path."/temp/";
		$this->clear_cache($cache_dir);
		
		$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM;
		$cacheSettings = array(
				'dir' => $cache_dir
		);
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
		
		$this->objPHPExcel = new PHPExcel();
		$this->active_sheet = 0;
	}
	
	public function clear_cache($cache_dir) {
		//Existence du répertoire
		if(file_exists($cache_dir)){
			$array_files = scandir($cache_dir);
			if ((is_array($array_files)) && (count($array_files))) {
				foreach ($array_files as $file) {
					//Le fichier est-il un cache de la classe PHPExcel
					if (preg_match('#^PHPExcel\..+\.cache$#',$file)) {
						//Le fichier a-t-il plus d'une heure ?
						$time_file = filemtime($cache_dir.$file);
						if((time()-$time_file)>=3600){
							//On le supprime
							unlink($cache_dir.$file);
						}
					}
				}
			}
		}
	}
	
	public function get_active_sheet() {
		return $this->active_sheet;
	}
	
	public function set_active_sheet($sheet = 0) {
		$this->active_sheet = $sheet;
	}
	
	public function set_column($first, $last, $width) {
		for ($i=$first; $i<=$last; $i++) {
			$this->objPHPExcel->setActiveSheetIndex($this->active_sheet)->getColumnDimensionByColumn($i)->setWidth($width);
		}
	}
	
	public function merge_cells($row1, $col1, $row2, $col2) {
		$this->objPHPExcel->setActiveSheetIndex($this->active_sheet)->mergeCellsByColumnAndRow($col1, $row1+1, $col2, $row2+1);
	}
	
	public function write_string($row, $col, $value, $styleArray=array()) {
		global $charset;
		
		if($charset != 'utf-8'){
			$value = iconv("CP1252", "UTF-8//TRANSLIT", $value);
		}
		if (trim($value)) {
			$this->objPHPExcel->setActiveSheetIndex($this->active_sheet)->setCellValueExplicitByColumnAndRow($col, $row+1, $value, PHPExcel_Cell_DataType::TYPE_STRING);
		}
		if (count($styleArray)) {
			$this->objPHPExcel->setActiveSheetIndex($this->active_sheet)->getStyleByColumnAndRow($col, $row+1)->applyFromArray($styleArray);
		}
	}
	
	public function write($row, $col, $value, $styleArray=array()){
		global $charset;
		
		if($charset != 'utf-8'){
			$value = iconv("CP1252", "UTF-8//TRANSLIT", $value);
		}
		if (trim($value)) {
			$this->objPHPExcel->setActiveSheetIndex($this->active_sheet)->setCellValueByColumnAndRow($col, $row+1, $value);
		}
		if (count($styleArray)) {
			$this->objPHPExcel->setActiveSheetIndex($this->active_sheet)->getStyleByColumnAndRow($col, $row+1)->applyFromArray($styleArray);
		}
	}
	
	public function download($filename){
		//On force en xlsx pour compatibilité avec les tableurs
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		if ($extension = "xls") {
			$filename = substr($filename,0,strlen($filename)-4).'.xlsx';
		}
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate');
		header ('Pragma: public');
			
		$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		$this->objPHPExcel->setActiveSheetIndex($this->active_sheet)->disconnectCells();
		exit;
	}
	
	public function save_file($filename){
		$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
		$objWriter->save($filename);
		$this->objPHPExcel->setActiveSheetIndex($this->active_sheet)->disconnectCells();
	}
}